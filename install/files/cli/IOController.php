<?php

class IOController
{
    public function __construct()
    {

    }
    public static function renderList(array $list, ?int $selected = null, string|null $query = null, int|null $page = null, int $limit = 10, string|null $searchLabel = "Type to search", string|null $prev_text = null)
    {
        $res = [];
        system('clear');
        if ($prev_text && !empty($prev_text))
            w($prev_text);
        if (count($list) > $limit) {
            $pagenList = [];
            $pagenCounter = 0;
            $curPage = 0;
            foreach ($list as $i => $value) {
                if ($pagenCounter < $limit) {
                    $pagenCounter++;
                    $pagenList[$curPage][] = $value;
                } else {
                    $pagenList[$curPage][] = $value;
                    $curPage++;
                    $pagenCounter = 0;
                }
            }
        }
        if ($pagenList) {
            if (is_null($page)) {
                $list = $pagenList[0];
            } else {
                $list = $pagenList[$page];
            }
        }

        if (!is_null($query)) {
            w("Search: " . ANSI_BACKGROUND_CYAN . $query . ANSI_CLOSE);
        } else {
            w(ANSI_BACKGROUND_CYAN . $searchLabel . ANSI_CLOSE);
        }

        if (!is_null($selected)) {
            $res = array_map(fn($q, $k) => ($k == $selected) ? ANSI_BACKGROUND_MAGENTA . $q . ANSI_CLOSE : $q, $list, array_keys($list));
        } else {
            $res = array_map(fn($q, $k) => $k == 0 ? ANSI_BACKGROUND_MAGENTA . $q . ANSI_CLOSE : $q, $list, array_keys($list));
        }
        arr($res);
        if ($pagenList) {
            $page = $page ?: 0;
            $page++;
            w("Page $page\\" . count($pagenList));
        }
    }
    private static function step(&$cup, &$cdown, $lenght)
    {
        $curval = $cdown - $cup;
        if ($curval < 0) {
            $curval = $lenght - 1;
            $cdown = $lenght - 1;
            $cup = 0;
        }
        if ($curval > ($lenght - 1)) {
            $curval = 0;
            $cup = 0;
            $cdown = 0;
        }
        return abs($curval);
    }
    private static function caclPage($list, int|null $page = null, int $limit = 10)
    {
        if (count($list) > $limit) {
            $pagenList = [];
            $pagenCounter = 0;
            $curPage = 0;
            foreach ($list as $i => $value) {
                if ($pagenCounter < $limit) {
                    $pagenCounter++;
                    $pagenList[$curPage][] = $value;
                } else {
                    $pagenList[$curPage][] = $value;
                    $curPage++;
                    $pagenCounter = 0;
                }
            }
            return !is_null($page) ? count($pagenList[$page]) : count($pagenList);
        } else if (is_null($page)) {
            return 1;
        } else {
            return count($list);
        }
    }
    public static function renderSelectMenu(array $list, int $limit = 10, string|null $searchLabel = "Type to search", string|null $prev_text = null)
    {
        $_limit = $limit;
        $list = array_values($list);
        self::renderList($list, null, null, null, $_limit, $searchLabel ?? "Type to search", $prev_text);
        $cup = 0;
        $cdown = 0;
        $pup = 0;
        $pdown = 0;
        $query = "";
        while ("ENTER" !== ($key = keyInput())) {
            switch ($key) {
                case 'UP':
                    $cup++;
                    break;
                case 'DOWN':
                    $cdown++;
                    break;
                case "LEFT":
                    $pup++;
                    $cup = 0;
                    $cdown = 0;
                    break;
                case "RIGHT":
                    $pdown++;
                    $cup = 0;
                    $cdown = 0;
                    break;
                default:
                    if (
                        !in_array($key, [
                            "UP",
                            "DOWN",
                            "RIGHT",
                            "LEFT",
                            "SPACE",
                            "ENTER",
                            "UNNOWN",
                            "BACKSPACE",
                            "TAB",
                            "ESC",
                        ])
                    ) {
                        $query .= $key;
                    } else if ($key == "BACKSPACE") {
                        $query = substr($query, 0, -1);
                    } else if ($key == "SPACE") {
                        $query .= " ";
                    }
                    break;
            }
            if (!empty($query)) {
                $filterList = array_values(array_filter($list, fn($q) => preg_match("/.*$query.*/i", $q)));
            } else {
                $filterList = $list;
            }
            $page = self::step($pup, $pdown, self::caclPage($filterList ?? $list, null, $_limit));
            $curval = self::step($cup, $cdown, self::caclPage($filterList ?? $list, $page, $_limit));
            self::renderList($filterList ?? $list, $curval, !empty($query) ? $query : null, $page, $_limit, $searchLabel ?? "Type to search", $prev_text);
        }
        return ($filterList ?? $list)[$curval ?? 0];
    }

    public static function inputLine(string $label = "")
    {
        $text = "";
        w($label);
        while ("ENTER" !== ($key = keyInput())) {
            system('clear');
            w($label);
            if (
                !in_array($key, [
                    "UP",
                    "DOWN",
                    "RIGHT",
                    "LEFT",
                    "SPACE",
                    "ENTER",
                    "UNNOWN",
                    "BACKSPACE",
                    "TAB",
                    "ESC",
                ])
            ) {
                $text .= $key;
            } else if ($key == "BACKSPACE") {
                $text = substr($text, 0, -1);
            } else if ($key == "SPACE") {
                $text .= " ";
            }
            system('stty sane');
            w($text);
        }
        return $text;
    }
}
?>
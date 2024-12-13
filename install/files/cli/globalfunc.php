<?php

function w($text)
{
    echo PHP_EOL;
    echo $text;
}
function e()
{
    echo PHP_EOL;
}
function arr(array $array)
{
    echo PHP_EOL . implode(PHP_EOL, array_map(fn($q) => "---$q", $array));
    e();
}
function translateKeypress($string)
{
    switch ($string) {
        case "\033[A":
            return "UP";
        case "\033[B":
            return "DOWN";
        case "\033[C":
            return "RIGHT";
        case "\033[D":
            return "LEFT";
        case "\n":
            return "ENTER";
        case " ":
            return "SPACE";
        case "\010":
            return "UNNOWN";
        case "\177":
            return "BACKSPACE";
        case "\t":
            return "TAB";
        case "\e":
            return "ESC";
    }
    return $string;
}
function keyInput()
{
    $stdin = fopen('php://stdin', 'r');
    stream_set_blocking($stdin, 0);
    system('stty cbreak -echo');

    while (1) {
        $keypress = fgets($stdin);
        if ($keypress) {
            fclose($stdin);
            system('stty sane');
            return translateKeypress($keypress);
        }
    }
}
?>
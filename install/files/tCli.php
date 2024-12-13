<?php
require_once __DIR__ . '/cli/define.php';
global $DOC_ROOT;
$DOC_ROOT = require_once(__DIR__ . '/docRoot.php');
require_once __DIR__ . '/cli/globalfunc.php';

class ReadDirs
{
    private array $bComponents = [];
    private $returnValue;
    private string $listLabel;
    private string $locale = "bitrix";
    private string $vendor = "bitrix";
    public function getRoot()
    {
        global $DOC_ROOT;
        return $DOC_ROOT;
    }
    public function __construct()
    {
    }
    public function getComponentArray(string $type): array
    {
        [$locale, $vendor] = explode("/", $type);
        $this->locale = $locale;
        $this->vendor = $vendor;
        return array_diff(array_filter(scandir($this->getRoot() . "/$locale/components/$vendor/"), fn($q) => is_dir($this->getRoot() . '/bitrix/components/bitrix/' . $q)), ['.', '..']);
    }
    public function getComponentTemplates(string $name)
    {
        return array_diff(array_filter(scandir($this->getRoot() . "/{$this->locale}/components/{$this->vendor}/$name/templates/"), fn($q) => is_dir($this->getRoot() . "/bitrix/components/bitrix/$name/templates/$q")), ['.', '..']);
    }
    public function getComponentTypes()
    {
        $res = [];
        if ($bdirs = scandir($this->getRoot() . '/bitrix/components/')) {
            $bdirs = array_diff($bdirs, ['..', '.']);
            $bdirs = array_filter($bdirs, fn($q) =>
                is_dir($this->getRoot() . '/bitrix/components/' . $q) &&
                (count(array_diff(array_filter(
                    scandir($this->getRoot() . "/bitrix/components/$q"),
                    fn($v) => is_dir($this->getRoot() . "/bitrix/components/$q/$v")
                ), ['..', '.'])) > 0));
            $bDirs = array_map(fn($q) => "bitrix/$q", $bdirs);
        }
        if ($ldirs = scandir($this->getRoot() . '/local/components/')) {
            $ldirs = array_diff($ldirs, ['..', '.']);
            $ldirs = array_filter($ldirs, fn($q) =>
                is_dir($this->getRoot() . '/local/components/' . $q) &&
                (count(array_diff(array_filter(
                    scandir($this->getRoot() . "/local/components/$q"),
                    fn($v) => is_dir($this->getRoot() . "/local/components/$q/$v")
                ), ['..', '.'])) > 0));
            $lDirs = array_map(fn($q) => "local/$q", $ldirs);

        }
        return [...$bDirs ?? [], ...$lDirs ?? []];
    }

    public function print()
    {
        if (is_array($this->returnValue)) {
            echo PHP_EOL . $this->listLabel . PHP_EOL;
            foreach (($array = array_diff(array_map(fn($q) => is_array($q) ? var_export($q, true) : $q . "", $this->returnValue), ['.', '..'])) as $k => $value) {
                echo PHP_EOL . $value;
                if ($k % 50 == 0) {
                    echo PHP_EOL . $k . "/" . count($array);
                    $stdin = fopen("php://stdin", "r");
                    fgets($stdin);
                    fclose($stdin);
                    system('clear');
                }
            }
            echo PHP_EOL;
        } else {
            echo $this->returnValue;
        }
    }
    public function get()
    {
        return $this->returnValue;
    }
}
class TransportDirs
{
    private array $config;
    private function getRoot()
    {
        global $DOC_ROOT;
        return $DOC_ROOT;
    }
    public function __construct()
    {
        global $config;
        $this->config = $config;
    }
    private function move($to, $from)
    {
        foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($from, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
            if ($item->isDir()) {
                mkdir($to . DIRECTORY_SEPARATOR . $iterator->getSubPathname());
            } else {
                copy($item, $to . DIRECTORY_SEPARATOR . $iterator->getSubPathname());
            }
            chmod($to . DIRECTORY_SEPARATOR . $iterator->getSubPathname(), 0755);
        }
    }
    public function createFullPath($path)
    {
        $path = str_replace($this->getRoot(), "", $path);
        $dirs = explode("/", $path);
        $dirs = array_filter($dirs, fn($q) => !empty ($q));
        $_path = $this->getRoot();
        foreach ($dirs as $dir) {
            $_path .= "/$dir";
            if (!is_dir($_path)) {
                mkdir($_path, 0755);
                chmod($_path, 0755);
            }
        }

    }
    public function copyTemplate(string $component, string $template)
    {

        $from = $this->getRoot() . "/bitrix/components/bitrix/$component/templates/$template";
        $newTemplateName = IOController::inputLine("Name template (skip for default):");
        $newTemplateName = !empty($newTemplateName) ? $newTemplateName : $template;
        $to = $this->getRoot() . "/{$this->config['directory']}/templates/{$this->config['site_template']}/components/bitrix/$component/$newTemplateName";
        $this->createFullPath($to);
        $this->move($to, $from);
    }
    public function copyComponent(string $component)
    {
        $from = $this->getRoot() . "/bitrix/components/bitrix/$component";
        $name = IOController::inputLine("Component name (skip for default):");
        $name = !empty($name) ? $name : $component;
        $to = $this->getRoot() . "/{$this->config['directory']}/components/{$this->config['vendor']}/$name";
        $this->createFullPath($to);
        $this->move($to, $from);

    }
    public function createModule()
    {
        $moduleName = IOController::inputLine("Module name: ");

    }

}
class CreateComponent
{
    private static function writeParams()
    {
        return "<?\r?>";
    }
    private static function writeDesc($name, $desc)
    {
        $res = "<?php \$arComponentDescription = [ \"NAME\" => \"$name\", \"DESCRIPTION\" => \"$desc\", \"PATH\" => [\"ID\" => \"$name\"] ];";

        return $res;
    }
    private static function writeTemplate()
    {
        return "<div>template</div>";
    }
    private static function writeJs()
    {
        return "";
    }
    private static function writeCss()
    {
        $res = "";
        return $res;
    }
    private static function writeClass($name, $mode)
    {
        global $config;
        $nameSpace = "{$config['vendor']}\\Component";
        $res = "<?php namespace $nameSpace; class $name extends \\CBitrixComponent implements \\Bitrix\\Main\\Engine\\Contract\\Controllerable { public function configureActions() { return [ /** \"functionName\" => [ \"prefilters\" => [ \r new \\Bitrix\\Main\\Engine\\ActionFilter\\Authentication(), \r new Bitrix\\Main\\Engine\\ActionFilter\\HttpMethod(array(Bitrix\\Main\\Engine\\ActionFilter\\HttpMethod::METHOD_GET, Bitrix\\Main\\Engine\\ActionFilter\\HttpMethod::METHOD_POST)), \r new \\Bitrix\\Main\\Engine\\ActionFilter\\Csrf(), ] ] */ ]; } public function executeComponent() { \$this->IncludeComponentTemplate(); } } ?>";
        return $res;
    }
    private static function writeLn()
    {
        return "";
    }
    public static function exec($name, $template, $desc, $componentDirName, $mode)
    {
        global $config;
        global $DOC_ROOT;
        switch ($mode) {
            case 'Full':
                $class = self::writeClass($name, $mode);
                $desc = self::writeDesc($name, $desc);
                $js = self::writeJs();
                $css = self::writeCss();
                $_template = self::writeTemplate();
                $_parameters = "<? \r\r ?>";
                $_lang = "<? \$MESS = []; \r\r ?>";
                break;
            case 'Short':
                $class = self::writeClass($name, $mode);
                $desc = self::writeDesc($name, $desc);
                $_template = self::writeTemplate();
                break;

            default:
                # code...
                break;
        }

        $tr = new TransportDirs();
        $componentPath = $DOC_ROOT . "/{$config['directory']}/components/{$config['vendor']}/$componentDirName";
        $tr->createFullPath($componentPath);

        if ($class)
            file_put_contents($componentPath . "/class.php", $class);

        file_put_contents($componentPath . "/.description.php", $desc);
        if ($_parameters) {
            file_put_contents($componentPath . "/.parameters.php", $_parameters);
            chmod($componentPath . "/.parameters.php", 0755);
        }
        $tr->createFullPath($componentPath . "/templates/$template");
        file_put_contents($componentPath . "/templates/$template/template.php", $_template);

        chmod($componentPath . "/class.php", 0755);
        chmod($componentPath . "/.description.php", 0755);
        chmod($componentPath . "/templates/$template/template.php", 0755);
        if ($css) {
            file_put_contents($componentPath . "/templates/$template/style.css", $css);
            chmod($componentPath . "/templates/$template/style.css", 0755);
        }
        if ($js) {
            file_put_contents($componentPath . "/templates/$template/script.js", $js);
            chmod($componentPath . "/templates/$template/script.js", 0755);
        }
        if ($_lang) {
            $tr->createFullPath($componentPath . "/templates/$template/lang/ru");
            file_put_contents($componentPath . "/templates/$template/lang/ru/template.php", $_lang);
            chmod($componentPath . "/templates/$template/lang/ru/template.php", 0755);
        }
    }
}
class CreateDirs
{

    public static function createComponent(string $name, string $templateName = ".default", string|null $description = " ", string $mode = "Full")
    {
        $componentDirName = preg_replace("/_/", ".", $name);
        system('clear');
        $configText =
            "Component: $name"
            . PHP_EOL
            . "Template: $templateName"
            . PHP_EOL
            . "Description: $description"
            . PHP_EOL
        ;
        $continue = IOController::renderSelectMenu([
            'Y',
            'N'
        ], 2, "Create", $configText);
        if ($continue !== "Y")
            StartFunctions::createComponent();

        CreateComponent::exec($name, $templateName, $description, $componentDirName, $mode);
    }
}

require_once __DIR__ . '/cli/IOController.php';

function setupConfig()
{
    global $DOC_ROOT;
    $beforeText = "Before we`re start, you need to configure" . PHP_EOL . PHP_EOL . "Please, choose a location of future files and dirs" . PHP_EOL;
    $directory = IOController::renderSelectMenu([
        'local',
        'bitrix'
    ], 10, "Directory", $beforeText);
    system('clear');
    while (empty($vendor = IOController::inputLine("Enter your vendor: "))) {
        system('clear');
    }
    while (empty($site_template = IOController::inputLine("Enter you site template: "))) {
        system('clear');
    }
    system("clear");
    $setupConfigText =
        "Here your config: "
        . PHP_EOL
        . "Components: $DOC_ROOT/$directory/components/$vendor/COMPONENT.NAME"
        . PHP_EOL
        . "Templates: $DOC_ROOT/$directory/templates/$site_template/components/bitrix/TEMPLATE_NAME"
        . PHP_EOL
        . "Modules: $DOC_ROOT/$directory/modules/$vendor.MODULE_NAME"
        . PHP_EOL
    ;
    $confirm = IOController::renderSelectMenu([
        "Y",
        "N"
    ], 2, "Continue:", $setupConfigText);
    switch ($confirm) {
        case 'Y':
            if (file_exists(__DIR__ . '/config.php')) {
                unlink(__DIR__ . '/config.php');
            }
            file_put_contents(__DIR__ . '/config.php', "<?php return [ \"vendor\" => \"$vendor\", \"directory\" => \"$directory\", \"site_template\" => \"$site_template\" ];?>");
            // system('clear');
            break;
        case 'N':
            setupConfig();
            break;
    }
}
class StartFunctions
{
    public static function createComponent()
    {
        $_mode = IOController::renderSelectMenu([
            'Full',
            'Short'
        ], 2, "Mode: ");
        w("Name your component:");
        $componentName = IOController::inputLine("Name: ");
        $componentName = preg_replace('/[\W\s!?]/', '_', $componentName);
        $componentName = preg_replace('/[\_]{2,}/', '.', $componentName);
        $templateName = ".default";
        system('clear');
        switch ($_mode) {
            case 'Full':
                w("Enter description");
                $componentDescription = IOController::inputLine("Description: ");
                $_defaultTemplateName = IOController::renderSelectMenu([
                    'Y',
                    'N'
                ], 2, "Default template name?");
                if ($_defaultTemplateName == "N") {
                    w("Enter template name");
                    $templateName = IOController::inputLine("Template name: ");
                } else {
                    $templateName = ".default";
                }
                break;
            case "Short":

                break;

            default:

                break;
        }
        CreateDirs::createComponent($componentName, $templateName, $componentDescription, $_mode);
    }
}

global $config;
try {
    $config = require_once(__DIR__ . '/config.php');
} catch (\Throwable $th) {
    w("Config is missing!");
}
if (!$config) {
    setupConfig();
    try {
        $config = require_once(__DIR__ . '/config.php');
        $wasSetuped = true;
    } catch (\Throwable $th) {
        w("Can`t create a config. Check the rights for folder:");
        w(ANSI_BACKGROUND_RED . __DIR__ . ANSI_CLOSE);
    }
}

switch ($argv[1]) {
    case 'start':
        system('clear');

        $answer = IOController::renderSelectMenu([
            ANSI_CYAN . ANSI_UNDERLINE . 'Copy Template' . ANSI_CLOSE,
            ANSI_CYAN . ANSI_UNDERLINE . 'Copy Full Component' . ANSI_CLOSE,
            ANSI_YELLOW . ANSI_UNDERLINE . 'Create Component' . ANSI_CLOSE,
            ANSI_YELLOW . ANSI_UNDERLINE . 'Create Module (WIP)' . ANSI_CLOSE,
        ], 10, null, "Welcome to Template Cli, first one you need it is choose a command");
        switch ($answer) {
            case ANSI_CYAN . ANSI_UNDERLINE . 'Copy Full Component' . ANSI_CLOSE:
                $dirs = new ReadDirs();
                $type = IOController::renderSelectMenu($dirs->getComponentTypes(), 40, null, "Choose - location/vendor");
                $component = IOController::renderSelectMenu($dirs->getComponentArray($type), 40, null, "Bitrix Components");
                (new TransportDirs())->copyComponent($component);
                break;
            case ANSI_CYAN . ANSI_UNDERLINE . 'Copy Template' . ANSI_CLOSE:
                $dirs = new ReadDirs();
                $type = IOController::renderSelectMenu($dirs->getComponentTypes(), 40, null, "Choose - location/vendor");
                $component = IOController::renderSelectMenu($dirs->getComponentArray($type), 40, null, "Template");
                $template = IOController::renderSelectMenu($dirs->getComponentTemplates($component));
                (new TransportDirs())->copyTemplate($component, $template);
                break;
            case ANSI_YELLOW . ANSI_UNDERLINE . 'Create Component' . ANSI_CLOSE:
                StartFunctions::createComponent();
                break;
            case ANSI_YELLOW . ANSI_UNDERLINE . 'Create Module' . ANSI_CLOSE:

                break;
        }
        break;
    case "config":
        switch ($argv[2]) {
            case 'clear':
                if (file_exists(__DIR__ . '/config.php')) {
                    unlink(__DIR__ . '/config.php');
                } else {
                    w("Config not exists");
                }
                break;
            case 'edit':
                if ($wasSetuped)
                    break;
                if (file_exists(__DIR__ . '/config.php')) {
                    unlink(__DIR__ . '/config.php');
                } else {
                    w("Config not exists");
                }
                setupConfig();
                try {
                    $config = require_once(__DIR__ . '/config.php');
                } catch (\Throwable $th) {
                    w("Can`t create a config. Check the rights for folder:");
                    w(ANSI_BACKGROUND_RED . __DIR__ . ANSI_CLOSE);
                }
                break;

            default:
                # code...
                break;
        }

        break;

    default:
        # code...
        break;
}

system('stty sane');


die();
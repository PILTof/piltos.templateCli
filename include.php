<?
Bitrix\Main\Loader::registerAutoloadClasses(
	// имя модуля
	"piltos.templateCli",
	array(
		// ключ - имя класса с простанством имен, значение - путь относительно корня сайта к файлу
		"piltos\\TemplateCli\\Main" => "lib/main.php",
		// файл инклудится за счет правильных имен, иначе будет ошибка при установке и удаленни модуля
		//"piltos\\d7\\DataTable" => "lib/data.php",
	)
);
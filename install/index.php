<?
// пространство имен для подключений ланговых файлов
use Bitrix\Main\Localization\Loc;
// пространство имен для управления (регистрации/удалении) модуля в системе/базе
use Bitrix\Main\ModuleManager;
// пространство имен для работы с параметрами модулей хранимых в базе данных
use Bitrix\Main\Config\Option;
// пространство имен с абстрактным классом для любых приложений, любой конкретный класс приложения является наследником этого абстрактного класса
use Bitrix\Main\Application;
// пространство имен для работы c ORM
use \Bitrix\Main\Entity\Base;
// пространство имен для автозагрузки модулей
use \Bitrix\Main\Loader;
// пространство имен для событий
use \Bitrix\Main\EventManager;
// подключение ланговых файлов
Loc::loadMessages(__FILE__);
class piltos_templateCli extends CModule
{
    // переменные модуля
    public $MODULE_ID;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    public $SHOW_SUPER_ADMIN_GROUP_RIGHTS;
    public $MODULE_GROUP_RIGHTS;
    public $errors;
    // конструктор класса, вызывается автоматически при обращение к классу
    function __construct()
    {
        // создаем пустой массив для файла version.php
        $arModuleVersion = array();
        // подключаем файл version.php
        include_once(__DIR__ . '/version.php');
        // версия модуля
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        // дата релиза версии модуля
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        // id модуля
        $this->MODULE_ID = "piltos.templateCli";
        // название модуля
        $this->MODULE_NAME = "Template CLI";
        // описание модуля
        $this->MODULE_DESCRIPTION = "Template CLI ";
        // имя партнера выпустившего модуль
        $this->PARTNER_NAME = "Piltos";
        // ссылка на рисурс партнера выпустившего модуль
        $this->PARTNER_URI = "https://piltos.wip.ru";
        // если указано, то на странице прав доступа будут показаны администраторы и группы
        $this->SHOW_SUPER_ADMIN_GROUP_RIGHTS = 'Y';
        // если указано, то на странице редактирования групп будет отображаться этот модуль
        $this->MODULE_GROUP_RIGHTS = 'Y';
    }
    // метод отрабатывает при установке модуля
    function DoInstall()
    {
        //*************************************//
        // Пример с установкой в один шаг      //
        //*************************************//
        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // // регистрируем модуль в системе
        ModuleManager::RegisterModule("piltos.templateCli");
        // создаем таблицы баз данных, необходимые для работы модуля
        // $this->InstallDB();
        // создаем первую и единственную запись в БД
        // $this->addData();
        // регистрируем обработчики событий
        // $this->InstallEvents();
        // копируем файлы, необходимые для работы модуля
        $this->InstallFiles();
        // устанавливаем агента
        // $this->installAgents();
        // подключаем скрипт с административным прологом и эпилогом
        // $APPLICATION->includeAdminFile(
        //     Loc::getMessage('INSTALL_TITLE'),
        //     __DIR__ . '/instalInfo.php'
        // );

        // для успешного завершения, метод должен вернуть true
        return true;
    }
    // метод отрабатывает при удалении модуля
    function DoUninstall()
    {
        //*************************************//
        // Пример с удалением в один шаг       //
        //*************************************//
        // глобальная переменная с обстрактным классом
        global $APPLICATION;
        // удаляем таблицы баз данных, необходимые для работы модуля
        // $this->UnInstallDB();
        // удаляем обработчики событий
        // $this->UnInstallEvents();
        // удаляем файлы, необходимые для работы модуля
        $this->UnInstallFiles();
        // удаляем агента
        // $this->unInstallAgents();
        // удаляем регистрацию модуля в системе
        ModuleManager::UnRegisterModule("piltos.templateCli");
        // подключаем скрипт с административным прологом и эпилогом
        // $APPLICATION->includeAdminFile(
        //     Loc::getMessage('DEINSTALL_TITLE'),
        //     __DIR__ . '/deInstalInfo.php'
        // );
        // для успешного завершения, метод должен вернуть true
        return true;
    }
    // метод для создания таблицы баз данных
    function InstallDB()
    {
        // подключаем модуль для того что бы был видем класс ORM
        Loader::includeModule($this->MODULE_ID);
        // через класс Application получаем соединение по переданному параметру, параметр берем из ORM-сущности (он указывается, если необходим другой тип подключения, отличный от default), если тип подключения по умолчанию, то параметр можно не передавать. Далее по подключению вызываем метод isTableExists, в который передаем название таблицы полученное с помощью метода getDBTableName() класса Base
        if (!Application::getConnection(\Hmarketing\d7\DataTable::getConnectionName())->isTableExists(Base::getInstance("\Hmarketing\d7\DataTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            // Base::getInstance("\Hmarketing\d7\DataTable")->createDbTable();
        }
        if (!Application::getConnection(\Hmarketing\d7\DataTable::getConnectionName())->isTableExists(Base::getInstance("\Hmarketing\d7\AuthorTable")->getDBTableName())) {
            // eсли таблицы не существует, то создаем её по ORM сущности
            // Base::getInstance("\Hmarketing\d7\AuthorTable")->createDbTable();
        }
    }
    // метод для удаления таблицы баз данных
    function UnInstallDB()
    {
        // подключаем модуль для того что бы был видем класс ORM
        Loader::includeModule($this->MODULE_ID);
        // делаем запрос к бд на удаление таблицы, если она существует, по подключению к бд класса Application с параметром подключения ORM сущности
        // Application::getConnection(\Hmarketing\d7\DataTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Hmarketing\d7\DataTable")->getDBTableName());
        // Application::getConnection(\Hmarketing\d7\DataTable::getConnectionName())->queryExecute('DROP TABLE IF EXISTS ' . Base::getInstance("\Hmarketing\d7\AuthorTable")->getDBTableName());
        // удаляем параметры модуля из базы данных битрикс
        Option::delete($this->MODULE_ID);
    }
    // метод для создания обработчика событий
    function InstallEvents()
    {
        // для произвольной работы
        EventManager::getInstance()->registerEventHandler(
            // идентификатор модуля-источника события
            $this->MODULE_ID,
            // событие на которое мы подписываемся, OnSomeEvent для произвольной работы
            "OnSomeEvent",
            // идентификатор модуля, который подписывается
            $this->MODULE_ID,
            // класс выполняющий обработку (для callback-обработчика, если файловый - пустая строка)
            "\Hmarketing\d7\Main",
            // метод класса выполняющий обработку (для callback-обработчика, если файловый - пустая строка)
            'get'
        );
        // для работы с ORM, есть три типа событий: onBefore<Action> - перед вызовом запроса (можно изменить входные параметры), после следуют валидаторы. on<Action> - уже нельзя изменить входные параметры, после выполняется SQL-запрос. onAfter<Action> - после выполнения операции, операция уже совершена
        // три события <Action> итого 9 событий: Add, Update, Delete
        EventManager::getInstance()->registerEventHandler(
            // идентификатор модуля, для которого регистрируется событие
            $this->MODULE_ID,
            // тип события, класс называется DataTable, но должно передаваться по имени файла, то есть просто Data
            "\Hmarketing\d7\Data::OnBeforeUpdate",
            // идентификатор модуля к которому относится регистрируемый обработчик, из какого модуля берется класс, нужно если необходимо связать 2 модуля, если используем один, то дублируем поле с первым
            $this->MODULE_ID,
            // класс обработчика
            "\Hmarketing\d7\Events",
            // метод обработчика
            'eventHandler'
        );
        // для успешного завершения, метод должен вернуть true
        return true;
    }
    // метод для удаления обработчика событий
    function UnInstallEvents()
    {
        // удаление событий, аналогично установке
        EventManager::getInstance()->unRegisterEventHandler(
            $this->MODULE_ID,
            "OnSomeEvent",
            $this->MODULE_ID,
            "\Hmarketing\d7\Main",
            'get'
        );
        // удаление событий, аналогично установке
        EventManager::getInstance()->unRegisterEventHandler(
            $this->MODULE_ID,
            "\Hmarketing\d7\Data::OnBeforeUpdate",
            $this->MODULE_ID,
            "\Hmarketing\d7\Events",
            'eventHandler'
        );
        // для успешного завершения, метод должен вернуть true
        return true;
    }
    // метод для копирования файлов модуля при установке
    function InstallFiles()
    {
        symlink(__DIR__ . '/files/tCli.php', $_SERVER['DOCUMENT_ROOT'] . '/tCli');
        if (file_exists(__DIR__ . '/files/docRoot.php')) {
            unlink(__DIR__ . '/files/docRoot.php');
        }
        file_put_contents(__DIR__ . '/files/docRoot.php', '<?php return "' . $_SERVER['DOCUMENT_ROOT'] . '";');
        // скопируем файлы на страницы админки из папки в битрикс, копирует одноименные файлы из одной директории в другую директорию
        CopyDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin",
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );
        // скопируем компоненты из папки в битрикс, копирует одноименные файлы из одной директории в другую директорию
        CopyDirFiles(
            __DIR__ . "/components",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components",
            true, // перезаписывает файлы
            true  // копирует рекурсивно
        );
        // копируем файлы страниц, копирует одноименные файлы из одной директории в другую директорию
        // CopyDirFiles(
        //     __DIR__ . '/files',
        //     $_SERVER["DOCUMENT_ROOT"] . '/',
        //     true, // перезаписывает файлы
        //     true  // копирует рекурсивно
        // );
        // для успешного завершения, метод должен вернуть true
        return true;
    }
    // метод для удаления файлов модуля при удалении
    function UnInstallFiles()
    {
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/tCli') && is_link($_SERVER['DOCUMENT_ROOT'] . 'tCli')) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/tCli');
        }
        unlink($_SERVER['DOCUMENT_ROOT'] . '/files/docRoot.php');
        // удалим файлы из папки в битрикс на страницы админки, удаляет одноименные файлы из одной директории, которые были найдены в другой директории, функция не работает рекурсивно
        DeleteDirFiles(
            __DIR__ . "/admin",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin"
        );
        // удалим компонент из папки в битрикс 
        if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/" . $this->MODULE_ID)) {
            // удаляет папка из указанной директории, функция работает рекурсивно
            DeleteDirFilesEx(
                "/bitrix/components/" . $this->MODULE_ID
            );
        }
        // удалим файлы страниц, удаляет одноименные файлы из одной директории, которые были найдены в другой директории, функция не работает рекурсивно
        DeleteDirFiles(
            __DIR__ . "/files",
            $_SERVER["DOCUMENT_ROOT"] . "/"
        );
        // для успешного завершения, метод должен вернуть true
        return true;
    }
    // заполнение таблиц тестовыми данными
    function addData()
    {
        // подключаем модуль для видимости ORM класса
        Loader::includeModule($this->MODULE_ID);
        // добавляем запись в таблицу БД
        \Hmarketing\d7\DataTable::add(
            array(
                "ACTIVE" => "N",
                "SITE" => '["s1"]',
                "LINK" => " ",
                "LINK_PICTURE" => "/bitrix/components/hmarketing.d7/popup.baner/templates/.default/img/banner.jpg",
                "ALT_PICTURE" => " ",
                "EXCEPTIONS" => " ",
                "DATE" => new \Bitrix\Main\Type\DateTime(date("d.m.Y H:i:s")),
                "TARGET" => "self",
                "AUTHOR_ID" => "1",
            )
        );
        // добавляем запись в таблицу БД
        \Hmarketing\d7\AuthorTable::add(
            array(
                "NAME" => "Иван",
                "LAST_NAME" => "Иванов",
            )
        );
        // для успешного завершения, метод должен вернуть true
        return true;
    }
    // установка агентов
    function installAgents()
    {
        \CAgent::AddAgent(
            // строка PHP для запуска агента-функции
            "\Hmarketing\d7\Agent::superAgent();",
            // идентификатор модуля, необходим для подключения файлов модуля (необязательный) 
            $this->MODULE_ID,
            // период, нужен для агентов, которые должны выполняться точно в срок. Если агент пропустил запуск, то он сделает его столько раз, сколько он пропустил. Если значение N, то агент после первого запуска будет запускаться с заданным интервалам (необязательный, по умолчанию N)                   
            "N",
            // интервал в секундах (необязательный, по умолчанию 86400 (сутки))                                
            120,
            // дата первой проверки (необязательный, по умолчанию текущее время)
            "",
            // активность агента (необязательный, по умолчанию Y) 
            "Y",
            // дата первого запуска (необязательный, по умолчанию текущее время)
            "",
            // сортировка (влияет на порядок выполнения агентов (очередность), для тех, которые запускаются в одно время) (необязательный, по умолчанию 100)  
            100
        );
    }
    // удаление агентов
    function unInstallAgents()
    {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
    }
}
<?php

use IBS\Migration\Locale;

class ibs_migration extends CModule
{
    var $MODULE_ID = "ibs.migration";
    var $MODULE_NAME;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_DESCRIPTION;
    var $PARTNER_NAME;
    var $PARTNER_URI;
    var $MODULE_GROUP_RIGHTS = "Y";

    function ibs_migration()
    {
        $arModuleVersion = [];

        include(__DIR__ . "/version.php");

        $this->MODULE_VERSION = $arModuleVersion["VERSION"];
        $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        include(__DIR__ . '/../locale/ru.php');
        include(__DIR__ . '/../locale/en.php');

        $this->MODULE_NAME = GetMessage("IBS_MIGRATION_RU_MODULE_NAME");
        $this->MODULE_DESCRIPTION = GetMessage("IBS_MIGRATION_RU_MODULE_DESCRIPTION");
        $this->PARTNER_NAME = GetMessage("IBS_MIGRATION_RU_PARTNER_NAME");
        $this->PARTNER_URI = GetMessage("IBS_MIGRATION_RU_PARTNER_URI");
    }

    function DoInstall()
    {
        RegisterModule($this->MODULE_ID);

        CopyDirFiles(__DIR__ . "/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
        $this->installGadgets();
        $this->updateMigrationTables();
    }

    function DoUninstall()
    {
        DeleteDirFiles(__DIR__ . "/admin", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
        $this->unnstallGadgets();

        UnRegisterModule($this->MODULE_ID);
    }

    function installGadgets()
    {
        CopyDirFiles(__DIR__ . "/gadgets", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/gadgets", true, true);
    }

    function unnstallGadgets()
    {
        DeleteDirFiles(__DIR__ . "/gadgets", $_SERVER["DOCUMENT_ROOT"] . "/bitrix/gadgets");
    }

    function GetModuleRightList()
    {
        $arr = [
            "reference_id" => ["D", "W"],
            "reference"    => [
                "[D] " . Locale::getMessage("RIGHT_D"),
                "[W] " . Locale::getMessage("RIGHT_W"),
            ],
        ];
        return $arr;
    }

    function updateMigrationTables()
    {
        global $DB;
        $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE 'sprint_migration%' AND TABLE_SCHEMA=DATABASE();";
        $result = $DB->Query($sql);
        while ($row = $result->Fetch()) {
            $DB->Query("RENAME TABLE ".$row['TABLE_NAME']." TO ".str_replace('sprint_migration','ibs_migration', $row['TABLE_NAME']).";");
        }
    }
}

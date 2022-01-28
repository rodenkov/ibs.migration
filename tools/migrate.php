<?php

use Bitrix\Main\Loader;

if (php_sapi_name() != 'cli') {
    die('Can not run in this mode. Bye!');
}

set_time_limit(0);
error_reporting(E_ERROR);


defined('NO_AGENT_CHECK') || define('NO_AGENT_CHECK', true);
defined('NO_KEEP_STATISTIC') || define('NO_KEEP_STATISTIC', "Y");
defined('NO_AGENT_STATISTIC') || define('NO_AGENT_STATISTIC', "Y");
defined('NOT_CHECK_PERMISSIONS') || define('NOT_CHECK_PERMISSIONS', true);

if (empty($_SERVER["DOCUMENT_ROOT"])) {
    $_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . '/../../../../');
}

$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

try {

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

    if (!Loader::includeModule('ibs.migration')) {
        /** @var ibs_migration $ob */
        if ($ob = CModule::CreateModuleObject('ibs.migration')) {
            $ob->DoInstall();
        }
    }

    if (!Loader::includeModule('ibs.migration')) {
        Throw new Exception('need to install module ibs.migration');
    }

    IBS\Migration\Module::checkHealth();

    $console = new IBS\Migration\Console($argv);
    $console->executeConsoleCommand();

    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");

} catch (Exception $e) {
    fwrite(STDOUT, sprintf(
        "[%s] %s (%s)\n%s\n",
        get_class($e),
        $e->getMessage(),
        $e->getCode(),
        $e->getTraceAsString()
    ));

    die(1);

} catch (Throwable $e) {
    fwrite(STDOUT, sprintf(
        "[%s] %s (%s)\n%s\n",
        get_class($e),
        $e->getMessage(),
        $e->getCode(),
        $e->getTraceAsString()
    ));

    die(1);
}





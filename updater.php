<?php

/** @var CUpdater $updater */
if ($updater && $updater instanceof CUpdater) {

    if (!function_exists('ibs_migration_rmdir')) {
        function ibs_migration_rmdir($dir)
        {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? ibs_migration_rmdir("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
    }

    if (!empty($_SERVER['DOCUMENT_ROOT'])) {
//        ibs_migration_rmdir($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/ibs.migration/lib/helpers/useroptions/');
//        ibs_migration_rmdir($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/ibs.migration/admin/');
//        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/ibs.migration/loader.php');
    }

    if (is_dir(__DIR__ . '/install/gadgets/')) {
        $updater->CopyFiles("install/gadgets/", "gadgets/");
    }

}
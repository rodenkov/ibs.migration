<?php

if (is_file($_SERVER["DOCUMENT_ROOT"] . "/local/modules/ibs.migration/admin/ibs_migrations.php")) {
    /** @noinspection PhpIncludeInspection */
    require($_SERVER["DOCUMENT_ROOT"] . "/local/modules/ibs.migration/admin/ibs_migrations.php");
} else {
    /** @noinspection PhpIncludeInspection */
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/ibs.migration/admin/ibs_migrations.php");
}

<?php

use IBS\Migration\Enum\VersionEnum;
use IBS\Migration\Locale;
use IBS\Migration\Module;

global $APPLICATION;
$isSettinsPage = strpos($APPLICATION->GetCurPage(), 'settings.php');
?>
<div class="sp-group">
    <div class="sp-group-row2">
        <div class="sp-block">
            <div style="margin-bottom: 10px;">
                <?= Locale::getMessage('MODULE_VERSION') ?>: <?= Module::getVersion() ?>
            </div>
            <div style="margin-bottom: 10px;">
                <?php if ($isSettinsPage): ?>
                    <a href="/bitrix/admin/ibs_migrations.php?config=<?= VersionEnum::CONFIG_DEFAULT ?>&lang=<?= LANGUAGE_ID ?>"><?= Locale::getMessage('GOTO_MIGRATION') ?></a>
                <?php else: ?>
                    <a href="/bitrix/admin/settings.php?mid=ibs.migration&mid_menu=1&lang=<?= LANGUAGE_ID ?>"><?= Locale::getMessage('GOTO_OPTIONS') ?></a>
                <?php endif; ?>
            </div>
        </div>
        <div class="sp-block">
            <div style="margin-bottom: 10px;">
                <?= Locale::getMessage('LINK_MP') ?> <br/>
            </div>
            <div style="margin-bottom: 10px;">
                <?= Locale::getMessage('LINK_COMPOSER') ?>
                <br/>
                <a href="https://packagist.org/packages/rodenkov/ibs.migration" target="_blank">https://packagist.org/packages/rodenkov/ibs.migration</a>
            </div>
            <div style="margin-bottom: 10px;">
                <?= Locale::getMessage('LINK_DOC') ?>
                <br/>
                <a href="https://github.com/rodenkov/ibs.migration/wiki" target="_blank">https://github.com/rodenkov/ibs.migration/wiki</a>
            </div>
            <div style="margin-bottom: 10px;">
                <?= Locale::getMessage('LINK_ARTICLES') ?>
                <br/>
                <a href="https://dev.1c-bitrix.ru/community/webdev/user/39653/blog/" target="_blank">https://dev.1c-bitrix.ru/community/webdev/user/39653/blog/</a>
            </div>
            <div style="margin-bottom: 10px;">
                <?= Locale::getMessage('LINK_TELEGRAM') ?>
                <br/>
                <a href="https://t.me/ibs_migration_bitrix">https://t.me/ibs_migration_bitrix</a>
            </div>
        </div>
    </div>
</div>

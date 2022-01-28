<?php
global $APPLICATION;

use Bitrix\Main\Loader;
use IBS\Migration\Enum\VersionEnum;
use IBS\Migration\Locale;

if ($APPLICATION->GetGroupRight('ibs.migration') == 'D') {
    return false;
}

if (!Loader::includeModule('ibs.migration')) {
    return false;
}

try {
    $versionConfig = new IBS\Migration\VersionConfig();
    $configList = $versionConfig->getList();


    $schemas = [];
    foreach ($configList as $item) {
        $schemas[] = [
            'text' => $item['schema_title'],
            'url' => 'ibs_migrations.php?' . http_build_query([
                    'schema' => $item['name'],
                    'lang' => LANGUAGE_ID,
                ]),
        ];
    }

    $items = [];
    foreach ($configList as $item) {
        $items[] = [
            'text' => $item['title'],
            'url' => 'ibs_migrations.php?' . http_build_query([
                    'config' => $item['name'],
                    'lang' => LANGUAGE_ID,
                ]),
        ];
    }

    $items[] = [
        'items_id' => 'sp-menu-schema',
        'text' => Locale::getMessage('MENU_SCHEMAS'),
        'items' => $schemas,
    ];

    $aMenu = [
        'parent_menu' => 'global_menu_settings',
        'section' => 'IBS',
        'sort' => 50,
        'text' => Locale::getMessage('MENU_IBS'),
        'icon' => 'sys_menu_icon',
        'page_icon' => 'sys_page_icon',
        'items_id' => 'ibs_migrations',
        'items' => $items,
    ];

    return $aMenu;

} catch (Exception $e) {
    $aMenu = [
        'parent_menu' => 'global_menu_settings',
        'section' => 'IBS',
        'sort' => 50,
        'text' => Locale::getMessage('MENU_IBS'),
        'icon' => 'sys_menu_icon',
        'page_icon' => 'sys_page_icon',
        'items_id' => 'ibst_migrations',
        'url' => 'ibs_migrations.php?' . http_build_query([
                'config' => VersionEnum::CONFIG_DEFAULT,
                'lang' => LANGUAGE_ID,
            ]),
    ];

    return $aMenu;
} catch (Throwable $e) {
    $aMenu = [
        'parent_menu' => 'global_menu_settings',
        'section' => 'IBS',
        'sort' => 50,
        'text' => Locale::getMessage('MENU_IBS'),
        'icon' => 'sys_menu_icon',
        'page_icon' => 'sys_page_icon',
        'items_id' => 'ibs_migrations',
        'url' => 'ibs_migrations.php?' . http_build_query([
                'config' => VersionEnum::CONFIG_DEFAULT,
                'lang' => LANGUAGE_ID,
            ]),
    ];

    return $aMenu;
}

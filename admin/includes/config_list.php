<?php
/** @var $versionConfig VersionConfig */

use IBS\Migration\Locale;
use IBS\Migration\VersionConfig;

$versionConfig = new VersionConfig();
$configList = $versionConfig->getList();

?>

<?php foreach ($configList as $configItem): ?><?php

    $configValues = $versionConfig->humanValues($configItem['values']);

    ?>
    <div class="sp-group">
        <div class="sp-group-row">
            <div class="sp-block sp-white">
                <h3><?= Locale::getMessage('CONFIG') ?>: <?= $configItem['title'] ?></h3>
                <table class="sp-config">
                    <?php foreach ($configValues as $key => $val) : ?>
                        <tr>
                            <td><?= Locale::getMessage('CONFIG_' . $key) ?></td>
                            <td><?= $key ?></td>
                            <td><?= nl2br($val) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
<?php endforeach; ?>

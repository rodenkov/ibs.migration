<?php

namespace IBS\Migration\Builders;

use IBS\Migration\Exceptions\ExchangeException;
use IBS\Migration\Exceptions\HelperException;
use IBS\Migration\Exceptions\MigrationException;
use IBS\Migration\Exceptions\RebuildException;
use IBS\Migration\Exceptions\RestartException;
use IBS\Migration\Locale;
use IBS\Migration\Module;
use IBS\Migration\VersionBuilder;

class HlblockElementsBuilder extends VersionBuilder
{
    /**
     * @return bool
     */
    protected function isBuilderEnabled()
    {
        return (!Locale::isWin1251() && $this->getHelperManager()->Hlblock()->isEnabled());
    }

    protected function initialize()
    {
        $this->setTitle(Locale::getMessage('BUILDER_HlblockElementsExport1'));
        $this->setDescription(Locale::getMessage('BUILDER_HlblockElementsExport2'));
        $this->setGroup('Hlblock');

        $this->addVersionFields();
    }

    /**
     * @throws ExchangeException
     * @throws HelperException
     * @throws RebuildException
     * @throws RestartException
     * @throws MigrationException
     */
    protected function execute()
    {
        $hlblockId = $this->addFieldAndReturn(
            'hlblock_id',
            [
                'title'       => Locale::getMessage('BUILDER_HlblockElementsExport_HlblockId'),
                'placeholder' => '',
                'width'       => 250,
                'select'      => $this->getHelperManager()->HlblockExchange()->getHlblocksStructure(),
            ]
        );

        $this->getExchangeManager()
             ->HlblockElementsExport()
             ->setLimit(20)
             ->setExportFields(
                 $this->getHelperManager()->HlblockExchange()->getHlblockFieldsCodes($hlblockId)
             )
             ->setHlblockId($hlblockId)
             ->setExchangeFile(
                 $this->getVersionResourceFile(
                     $this->getVersionName(),
                     'hlblock_elements.xml'
                 )
             )->execute();

        $this->createVersionFile(
            Module::getModuleDir() . '/templates/HlblockElementsExport.php'
        );
    }
}

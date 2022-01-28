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

class MedialibElementsBuilder extends VersionBuilder
{
    /**
     * @return bool
     */
    protected function isBuilderEnabled()
    {
        return (!Locale::isWin1251() && $this->getHelperManager()->MedialibExchange()->isEnabled());
    }

    protected function initialize()
    {
        $this->setTitle(Locale::getMessage('BUILDER_MedialibElements1'));
        $this->setDescription(Locale::getMessage('BUILDER_MedialibElements2'));
        $this->setGroup('Medialib');

        $this->addVersionFields();
    }

    /**
     * @throws RebuildException
     * @throws ExchangeException
     * @throws RestartException
     * @throws HelperException
     * @throws MigrationException
     */
    protected function execute()
    {
        $medialibExchange = $this->getHelperManager()->MedialibExchange();
        $collectionIds = $this->addFieldAndReturn(
            'collection_id',
            [
                'title'       => Locale::getMessage('BUILDER_MedialibElements_CollectionId'),
                'placeholder' => '',
                'width'       => 250,
                'select'      => $medialibExchange->getCollectionStructure(
                    $medialibExchange::TYPE_IMAGE
                ),
                'multiple'    => true,
            ]
        );

        $this->getExchangeManager()
             ->MedialibElementsExport()
             ->setLimit(20)
             ->setCollectionIds($collectionIds)
             ->setExchangeFile(
                 $this->getVersionResourceFile(
                     $this->getVersionName(),
                     'medialib_elements.xml'
                 )
             )->execute();

        $this->createVersionFile(
            Module::getModuleDir() . '/templates/MedialibElementsExport.php'
        );
    }


}

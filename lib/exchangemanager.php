<?php

namespace Sprint\Migration;

use Sprint\Migration\Exchange\HlblockElementsExport;
use Sprint\Migration\Exchange\HlblockElementsImport;
use Sprint\Migration\Exchange\IblockElementsExport;
use Sprint\Migration\Exchange\IblockElementsImport;
use Sprint\Migration\Exchange\MedialibElementsExport;
use Sprint\Migration\Exchange\MedialibElementsImport;

class ExchangeManager
{
    protected $exchangeEntity;

    public function __construct(ExchangeEntity $exchangeEntity)
    {
        $this->exchangeEntity = $exchangeEntity;
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return IblockElementsExport
     */
    public function IblockElementsExport(): IblockElementsExport
    {
        return new IblockElementsExport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return IblockElementsImport
     */
    public function IblockElementsImport(): IblockElementsImport
    {
        return new IblockElementsImport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return HlblockElementsImport
     */
    public function HlblockElementsImport(): HlblockElementsImport
    {
        return new HlblockElementsImport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return HlblockElementsExport
     */
    public function HlblockElementsExport(): HlblockElementsExport
    {
        return new HlblockElementsExport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return MedialibElementsExport
     */
    public function MedialibElementsExport(): MedialibElementsExport
    {
        return new MedialibElementsExport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return MedialibElementsImport
     */
    public function MedialibElementsImport(): MedialibElementsImport
    {
        return new MedialibElementsImport($this->exchangeEntity);
    }
}

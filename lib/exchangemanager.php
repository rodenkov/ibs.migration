<?php

namespace IBS\Migration;

use IBS\Migration\Exchange\HlblockElementsExport;
use IBS\Migration\Exchange\HlblockElementsImport;
use IBS\Migration\Exchange\IblockElementsExport;
use IBS\Migration\Exchange\IblockElementsImport;
use IBS\Migration\Exchange\MedialibElementsExport;
use IBS\Migration\Exchange\MedialibElementsImport;

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
    public function IblockElementsExport()
    {
        return new IblockElementsExport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return IblockElementsImport
     */
    public function IblockElementsImport()
    {
        return new IblockElementsImport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return HlblockElementsImport
     */
    public function HlblockElementsImport()
    {
        return new HlblockElementsImport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return HlblockElementsExport
     */
    public function HlblockElementsExport()
    {
        return new HlblockElementsExport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return MedialibElementsExport
     */
    public function MedialibElementsExport()
    {
        return new MedialibElementsExport($this->exchangeEntity);
    }

    /**
     * @throws Exceptions\ExchangeException
     * @return MedialibElementsImport
     */
    public function MedialibElementsImport()
    {
        return new MedialibElementsImport($this->exchangeEntity);
    }
}

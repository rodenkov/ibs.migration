<?php

namespace Sprint\Migration;

use Bitrix\Main\DB\SqlQueryException;
use Sprint\Migration\Traits\HelperManagerTrait;

abstract class Version extends ExchangeEntity
{
    use HelperManagerTrait;

    protected $description   = "";
    protected $moduleVersion = "";
    protected $versionFilter = [];
    protected $storageName   = 'default';

    abstract public function up();

    abstract public function down();

    public function isVersionEnabled(): bool
    {
        return true;
    }

    public function getVersionName(): string
    {
        return $this->getClassName();
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getModuleVersion(): string
    {
        return $this->moduleVersion;
    }

    public function getVersionFilter(): array
    {
        return $this->versionFilter;
    }

    /**
     * @throws SqlQueryException
     */
    public function saveData($name, $data)
    {
        $this->getStorageManager()->saveData($this->getVersionName(), $name, $data);
    }

    /**
     * @throws SqlQueryException
     */
    public function getSavedData($name)
    {
        return $this->getStorageManager()->getSavedData($this->getVersionName(), $name);
    }

    /**
     * @param bool $name
     *
     * @throws SqlQueryException
     */
    public function deleteSavedData($name = '')
    {
        $this->getStorageManager()->deleteSavedData($this->getVersionName(), $name);
    }

    protected function getStorageManager(): StorageManager
    {
        return new StorageManager($this->storageName);
    }

    protected function getExchangeManager(): ExchangeManager
    {
        return new ExchangeManager($this);
    }
}




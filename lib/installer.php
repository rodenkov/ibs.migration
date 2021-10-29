<?php

namespace Sprint\Migration;

use Bitrix\Main\DB\SqlQueryException;
use Exception;
use Sprint\Migration\Enum\VersionEnum;
use Sprint\Migration\Exceptions\MigrationException;

class Installer
{
    private $versionManager;

    /**
     * Installer constructor.
     *
     * @param array $configValues
     *
     * @throws Exception
     */
    public function __construct(array $configValues = [])
    {
        $this->versionManager = new VersionManager(
            new VersionConfig('installer', $configValues)
        );
    }

    /**
     * @throws MigrationException
     * @throws SqlQueryException
     */
    public function up()
    {
        $this->executeAll(
            [
                'status' => VersionEnum::STATUS_NEW,
            ]
        );
    }

    /**
     * @throws MigrationException
     * @throws SqlQueryException
     */
    public function down()
    {
        $this->executeAll(
            [
                'status' => VersionEnum::STATUS_INSTALLED,
            ]
        );
    }

    /**
     * @param $filter
     *
     * @throws MigrationException
     * @throws SqlQueryException
     */
    protected function executeAll($filter)
    {
        $versions = $this->versionManager->getVersions($filter);
        $action = ($filter['status'] == VersionEnum::STATUS_NEW) ? VersionEnum::ACTION_UP : VersionEnum::ACTION_DOWN;

        foreach ($versions as $item) {
            $this->executeVersion($item['version'], $action);
        }
    }

    /**
     * @param string $version
     * @param string $action
     *
     * @throws MigrationException
     * @return bool
     */
    protected function executeVersion(string $version, string $action = VersionEnum::ACTION_UP): bool
    {
        $params = [];
        do {
            $exec = 0;

            $success = $this->versionManager->startMigration(
                $version,
                $action,
                $params
            );

            $restart = $this->versionManager->needRestart($version);

            if ($restart) {
                $params = $this->versionManager->getRestartParams($version);
                $exec = 1;
            }

            if (!$success && !$restart) {
                throw new MigrationException(
                    $this->versionManager->getLastException()->getMessage()
                );
            }
        } while ($exec == 1);

        return $success;
    }
}

<?php

namespace Sprint\Migration;

use Bitrix\Main\DB\SqlQueryException;
use Sprint\Migration\Tables\StorageTable;

class StorageManager extends StorageTable
{
    /**
     * @param string       $category
     * @param string       $name
     * @param string|array $value
     *
     * @throws SqlQueryException
     */
    public function saveData(string $category, string $name, $value)
    {
        $category = $this->forSql($category);
        $name = $this->forSql($name);

        if (!empty($category) && !empty($name)) {
            if (!empty($value)) {
                $value = $this->forSql(serialize($value));
                $this->query(
                /** @lang Text */
                    'INSERT INTO `#TABLE1#` (`category`,`name`, `data`) VALUES ("%s", "%s", "%s") 
                    ON DUPLICATE KEY UPDATE data = "%s"',
                    $category,
                    $name,
                    $value,
                    $value
                );
            }
        }
    }

    /**
     * @param string $category
     * @param string $name
     *
     * @throws SqlQueryException
     * @return array|string
     */
    public function getSavedData(string $category, string $name)
    {
        $category = $this->forSql($category);
        $name = $this->forSql($name);

        if (!empty($category) && !empty($name)) {
            $value = $this->query(
            /** @lang Text */
                'SELECT name, data FROM #TABLE1# WHERE `category` = "%s" AND `name` = "%s"',
                $category,
                $name
            )->Fetch();
            if ($value && $value['data']) {
                return unserialize($value['data']);
            }
        }
        return '';
    }

    /**
     * @param string $category
     * @param string $name
     *
     * @throws SqlQueryException
     */
    public function deleteSavedData(string $category, string $name = '')
    {
        $category = $this->forSql($category);

        if ($category && $name) {
            $name = $this->forSql($name);
            $this->query(
            /** @lang Text */
                'DELETE FROM `#TABLE1#` WHERE `category` = "%s" AND `name` = "%s"',
                $category,
                $name
            );
        } elseif ($category) {
            $this->query(
            /** @lang Text */
                'DELETE FROM `#TABLE1#` WHERE `category` = "%s"',
                $category
            );
        }
    }
}




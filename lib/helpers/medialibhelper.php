<?php

namespace Sprint\Migration\Helpers;

use CMedialib;
use CMedialibCollection;
use CMedialibItem;
use CTask;
use Sprint\Migration\Exceptions\HelperException;
use Sprint\Migration\Helper;

/**
 * Class MedialibHelper
 *
 * @package Sprint\Migration\Helpers
 */
class MedialibHelper extends Helper
{
    const TYPE_IMAGE = 'image';

    public function __construct()
    {
        parent::__construct();

        CMedialib::Init();
    }

    public function isEnabled()
    {
        return $this->checkModules(['fileman']);
    }

    public function getTypes()
    {
        return CMedialib::GetTypes();
    }

    /**
     * @param string $code
     *
     * @throws HelperException
     * @return int|void
     */
    public function getTypeIdByCode($code)
    {
        foreach ($this->getTypes() as $type) {
            if ($type['code'] == $code) {
                return (int)$type['id'];
            }
        }
        $this->throwException(__METHOD__, 'type not found');
    }

    /**
     * @param mixed $typeId
     * @param array $path
     *
     * @throws HelperException
     * @return int|void
     */
    public function getCollectionIdByNamePath($typeId, $path = [])
    {
        if (!is_numeric($typeId)) {
            $typeId = $this->getTypeIdByCode($typeId);
        }

        $parentId = 0;
        foreach ($path as $name) {
            $parentId = $this->getCollectionId(
                $typeId,
                [
                    'NAME'      => $name,
                    'PARENT_ID' => $parentId,
                ]
            );
        }
        if ($parentId) {
            return $parentId;
        }

        $this->throwException(__METHOD__, 'collection not found');
    }

    public function getCollections($typeId, $filter = [])
    {
        if (!is_numeric($typeId)) {
            $typeId = $this->getTypeIdByCode($typeId);
        }

        $filter['TYPES'] = [$typeId];

        $result = CMedialibCollection::GetList(
            [
                'arFilter' => $filter,
                'arOrder'  => ['ID' => 'asc'],
            ]
        );

        if (isset($filter['NAME'])) {
            //чистим результаты нечеткого поиска
            $result = array_filter(
                $result,
                function ($item) use ($filter) {
                    return ($item['NAME'] == $filter['NAME']);
                }
            );
        }
        return array_values($result);
    }

    /**
     * @param mixed        $typeId
     * @param array|string $name
     *
     * @throws HelperException
     * @return array|void
     */
    public function getCollection($typeId, $name)
    {
        $filter = is_array($name) ? $name : ['NAME' => $name];

        $result = $this->getCollections($typeId, $filter);

        if (!empty($result[0])) {
            return $result[0];
        }
        $this->throwException(__METHOD__, 'collection not found');
    }

    public function getCollectionId($typeId, $name)
    {
        $result = $this->getCollection($typeId, $name);

        return !empty($result['ID']) ? $result['ID'] : 0;
    }

    public function getElements($collectionId, $filter = [])
    {
        $itemFilter = [
            'arCollections' => [(int)$collectionId],
        ];

        $result = CMedialibItem::GetList($itemFilter);

        if (isset($filter['NAME'])) {
            //поиск по названию
            $result = array_filter(
                $result,
                function ($item) use ($filter) {
                    return ($item['NAME'] == $filter['NAME']);
                }
            );
        }

        return array_values($result);
    }

    /**
     * @param int          $collectionId
     * @param array|string $name
     *
     * @throws HelperException
     * @return array|void
     */
    public function getElement($collectionId, $name)
    {
        $filter = is_array($name) ? $name : ['NAME' => $name];

        $result = $this->getElements($collectionId, $filter);

        if (!empty($result[0])) {
            return $result[0];
        }
        $this->throwException(__METHOD__, 'element not found');
    }

    public function getElementId($collectionId, $name)
    {
        $result = $this->getElement($collectionId, $name);

        return !empty($result['ID']) ? $result['ID'] : 0;
    }

    public function addCollection($typeId, $fields)
    {
        $this->checkRequiredKeys(__METHOD__, $fields, ['NAME']);

        if (!is_numeric($typeId)) {
            $typeId = $this->getTypeIdByCode($typeId);
        }

        $fields = array_merge(
            [
                //'ID'          => 0, // ID элемента для обновления, 0 для добавления
                'NAME'        => '',
                'DESCRIPTION' => '',
                'OWNER_ID'    => $GLOBALS['USER']->GetId(),
                'PARENT_ID'   => 0,
                'KEYWORDS'    => '',
                'ACTIVE'      => 'Y',
                'ML_TYPE'     => '',
            ], $fields
        );

        $fields['ML_TYPE'] = $typeId;

        return CMedialibCollection::Edit(['arFields' => $fields]);
    }

    public function addElement($collectionId, $fields = [])
    {
        $this->checkRequiredKeys(__METHOD__, $fields, ['NAME', 'PATH']);

        $collectionId = is_array($collectionId) ? $collectionId : [$collectionId];

        $fields = array_merge(
            [
                'NAME'        => '',
                'DESCRIPTION' => '',
                'KEYWORDS'    => '',
                'PATH'        => '',
            ], $fields
        );

        if (!is_array($fields['PATH'])) {
            $fields['PATH'] = \CFile::MakeFileArray($fields['PATH']);
        }

        $fields = [
            'file'          => $fields['PATH'],
            'path'          => false,
            'arFields'      => [
                //'ID'          => 0, // ID элемента для обновления, 0 для добавления
                'NAME'        => $fields['NAME'],
                'DESCRIPTION' => $fields['DESCRIPTION'],
                'KEYWORDS'    => $fields['KEYWORDS'],
            ],
            'arCollections' => $collectionId,
        ];

        $result = CMedialibItem::Edit($fields);

        return !empty($result['ID']) ? $result['ID'] : 0;
    }

    public function deleteElement($id)
    {
        CMedialibItem::Delete($id, false, false);
    }

    public function deleteCollection($id)
    {
        CMedialibCollection::Delete($id, true);
    }

    /**
     * Получает права доступа к медиабиблиотеке для групп
     * возвращает массив вида [$groupId => $letter]
     * при $collectionId = 0 права запрашиваются для всех коллекций
     *
     * D - Доступ закрыт
     * F - Просмотр коллекций
     * R - Создание новых
     * V - Редактирование элементов
     * W - Редактирование элементов и коллекций
     * X - Полный доступ
     *
     * @param int $collectionId
     *
     * @return array
     */
    public function getGroupPermissions($collectionId = 0)
    {
        $collectionTree = CMedialib::GetCollectionTree(['CheckAccessFunk' => '__CanDoAccess']);
        $accessRights = CMedialib::GetAccessPermissionsArray($collectionId, $collectionTree['Collections']);

        $result = [];
        foreach ($accessRights as $groupId => $taskId) {
            $letter = CTask::GetLetter($taskId);
            if (empty($letter)) {
                continue;
            }
            $result[$groupId] = $letter;
        }

        return $result;
    }

    /**
     * Устанавливает права доступа к медиабиблиотеке для групп
     * предыдущие права сбрасываются
     * принимает массив вида [$groupId => $letter]
     * при $collectionId = 0 права устанавливаются для всех коллекций
     *
     * D - Доступ закрыт
     * F - Просмотр коллекций
     * R - Создание новых
     * V - Редактирование элементов
     * W - Редактирование элементов и коллекций
     * X - Полный доступ
     *
     * @param       $collectionId
     * @param array $permissions
     */
    public function setGroupPermissions($collectionId = 0, $permissions = [])
    {
        $accessRights = [];
        foreach ($permissions as $groupId => $letter) {
            $taskId = CTask::GetIdByLetter($letter, 'fileman', 'medialib');

            if (empty($taskId)) {
                continue;
            }

            $accessRights[$groupId] = $taskId;
        }

        CMedialib::SaveAccessPermissions($collectionId, $accessRights);
    }
}

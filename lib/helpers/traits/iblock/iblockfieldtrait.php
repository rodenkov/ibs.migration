<?php

namespace IBS\Migration\Helpers\Traits\Iblock;

use CIBlock;
use IBS\Migration\Locale;

trait IblockFieldTrait
{
    /**
     * Получает список полей инфоблока
     *
     * @param $iblockId
     *
     * @return array|bool
     */
    public function getIblockFields($iblockId)
    {
        return CIBlock::GetFields($iblockId);
    }

    /**
     * Сохраняет поля инфоблока
     *
     * @param       $iblockId
     * @param array $fields
     *
     * @return bool
     */
    public function saveIblockFields($iblockId, $fields = [])
    {
        $exists = CIBlock::GetFields($iblockId);

        $exportExists = $this->prepareExportIblockFields($exists);
        $fields = $this->prepareExportIblockFields($fields);

        $fields = array_replace_recursive($exportExists, $fields);

        if (empty($exists)) {
            $ok = $this->getMode('test') || $this->updateIblockFields($iblockId, $fields);
            $this->outNoticeIf(
                $ok,
                Locale::getMessage(
                    'IB_FIELDS_CREATED',
                    [
                        '#NAME#' => $iblockId,
                    ]
                )
            );
            return $ok;
        }

        if ($this->hasDiff($exportExists, $fields)) {
            $ok = $this->getMode('test') || $this->updateIblockFields($iblockId, $fields);
            $this->outNoticeIf(
                $ok,
                Locale::getMessage(
                    'IB_FIELDS_UPDATED',
                    [
                        '#NAME#' => $iblockId,
                    ]
                )
            );
            $this->outDiffIf($ok, $exportExists, $fields);
            return $ok;
        }

        if ($this->getMode('out_equal')) {
            $this->out(
                Locale::getMessage(
                    'IB_FIELDS_EQUAL',
                    [
                        '#NAME#' => $iblockId,
                    ]
                )
            );
        }

        return true;
    }

    /**
     * Получает список полей инфоблока
     * Данные подготовлены для экспорта в миграцию или схему
     *
     * @param $iblockId
     *
     * @return array
     */
    public function exportIblockFields($iblockId)
    {
        return $this->prepareExportIblockFields(
            $this->getIblockFields($iblockId)
        );
    }

    /**
     * Обновляет поля инфоблока
     *
     * @param $iblockId
     * @param $fields
     *
     * @return bool
     */
    public function updateIblockFields($iblockId, $fields)
    {
        if ($iblockId && !empty($fields)) {
            CIBlock::SetFields($iblockId, $fields);
            return true;
        }
        return false;
    }

    /**
     * @param $iblockId
     * @param $fields
     *
     * @deprecated
     */
    public function mergeIblockFields($iblockId, $fields)
    {
        $this->saveIblockFields($iblockId, $fields);
    }

    public function exportIblockElementFields($iblockId)
    {
        return $this->prepareExportIblockElementFields(
            $this->getIblockFields($iblockId)
        );
    }

    protected function prepareExportIblockFields($fields)
    {
        if (empty($fields)) {
            return $fields;
        }

        $exportFields = [];
        foreach ($fields as $code => $field) {
            if ($field['VISIBLE'] == 'N' || preg_match('/^(LOG_)/', $code)) {
                continue;
            }
            $exportFields[$code] = $field;
        }

        return $exportFields;
    }

    protected function prepareExportIblockElementFields($fields)
    {
        if (empty($fields)) {
            return $fields;
        }

        $exportFields = [];
        foreach ($fields as $code => $field) {
            if ($field['VISIBLE'] == 'N' || preg_match('/^(SECTION_|LOG_)/', $code)) {
                continue;
            }
            $exportFields[$code] = $field;
        }

        return $exportFields;
    }
}

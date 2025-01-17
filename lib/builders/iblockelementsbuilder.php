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

class IblockElementsBuilder extends VersionBuilder
{
    /**
     * @return bool
     */
    protected function isBuilderEnabled()
    {
        return (!Locale::isWin1251() && $this->getHelperManager()->Iblock()->isEnabled());
    }

    protected function initialize()
    {
        $this->setTitle(Locale::getMessage('BUILDER_IblockElementsExport1'));
        $this->setDescription(Locale::getMessage('BUILDER_IblockElementsExport2'));
        $this->setGroup('Iblock');

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
        $iblockId = $this->getFieldValueIblockId();
        $exportFilter = $this->getFieldValueExportFilter();

        $updateMode = $this->getFieldValueUpdateMode();
        $exportFields = $this->getFieldValueExportFields($iblockId, $updateMode);
        $exportProps = $this->getFieldValueExportProps($iblockId);

        $this->getExchangeManager()
             ->IblockElementsExport()
             ->setExportFilter($exportFilter)
             ->setExportFields($exportFields)
             ->setExportProperties($exportProps)
             ->setIblockId($iblockId)
             ->setLimit(20)
             ->setExchangeFile(
                 $this->getVersionResourceFile(
                     $this->getVersionName(),
                     'iblock_elements.xml'
                 )
             )->execute();

        $this->createVersionFile(
            Module::getModuleDir() . '/templates/IblockElementsExport.php',
            [
                'updateMode' => $updateMode,
            ]
        );
    }

    /**
     * @param $iblockId
     *
     * @throws RebuildException
     * @return array
     */
    protected function getFieldValueExportProps($iblockId)
    {
        $propsMode = $this->addFieldAndReturn(
            'props_mode',
            [
                'title'  => Locale::getMessage('BUILDER_IblockElementsExport_Properties'),
                'width'  => 250,
                'select' => [
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_SelectAll'),
                        'value' => 'all',
                    ],
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_SelectNone'),
                        'value' => 'none',
                    ],
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_SelectSome'),
                        'value' => 'some',
                    ],
                ],
            ]
        );

        if ($propsMode == 'some') {
            $exportProps = $this->addFieldAndReturn(
                'export_props',
                [
                    'title'    => Locale::getMessage('BUILDER_IblockElementsExport_Properties'),
                    'width'    => 250,
                    'multiple' => 1,
                    'value'    => [],
                    'select'   => $this->getHelperManager()->IblockExchange()->getIblockPropertiesStructure($iblockId),
                ]
            );
        } elseif ($propsMode == 'all') {
            $exportProps = $this->getHelperManager()->IblockExchange()->getIblockPropertiesStructure($iblockId);
            $exportProps = array_column($exportProps, 'value');
        } else {
            $exportProps = [];
        }

        return $exportProps;
    }

    /**
     * @throws RebuildException
     * @return array
     */
    protected function getFieldValueExportFilter()
    {
        $elementsMode = $this->addFieldAndReturn(
            'filter_mode',
            [
                'title'  => Locale::getMessage('BUILDER_IblockElementsExport_Filter'),
                'width'  => 250,
                'select' => [
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_SelectAll'),
                        'value' => 'all',
                    ],
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_SelectSomeId'),
                        'value' => 'list_id',
                    ],
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_SelectSomeXmlId'),
                        'value' => 'list_xml_id',
                    ],
                ],
            ]
        );

        if ($elementsMode == 'list_id') {
            $filterIds = $this->addFieldAndReturn(
                'export_filter_list_id', [
                    'title'  => Locale::getMessage('BUILDER_IblockElementsExport_FilterListId'),
                    'width'  => 350,
                    'height' => 40,
                ]
            );

            $exportFilter = [
                'ID' => $this->explodeString($filterIds),
            ];
        } elseif ($elementsMode == 'list_xml_id') {
            $filterXmlIds = $this->addFieldAndReturn(
                'export_filter_list_xml_id',
                [
                    'title'  => Locale::getMessage('BUILDER_IblockElementsExport_FilterListXmlId'),
                    'width'  => 350,
                    'height' => 40,
                ]
            );

            $exportFilter = [
                'XML_ID' => $this->explodeString($filterXmlIds),
            ];
        } else {
            $exportFilter = [];
        }

        return $exportFilter;
    }

    /**
     * @param      $iblockId
     *
     * @param bool $updateMode
     *
     * @throws RebuildException
     * @return array
     */
    protected function getFieldValueExportFields($iblockId, $updateMode = false)
    {
        $fieldsMode = $this->addFieldAndReturn(
            'fields_mode',
            [
                'title'  => Locale::getMessage('BUILDER_IblockElementsExport_Fields'),
                'width'  => 250,
                'select' => [
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_SelectAll'),
                        'value' => 'all',
                    ],
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_SelectNone'),
                        'value' => 'none',
                    ],
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_SelectSome'),
                        'value' => 'some',
                    ],
                ],
            ]
        );

        if ($fieldsMode == 'some') {
            $exportFields = $this->addFieldAndReturn(
                'export_filter', [
                    'title'    => Locale::getMessage('BUILDER_IblockElementsExport_Fields'),
                    'width'    => 250,
                    'multiple' => 1,
                    'value'    => [],
                    'select'   => $this->getHelperManager()->IblockExchange()->getIblockElementFieldsStructure($iblockId),
                ]
            );
        } elseif ($fieldsMode == 'all') {
            $exportFields = $this->getHelperManager()->IblockExchange()->getIblockElementFieldsStructure($iblockId);
            $exportFields = array_column($exportFields, 'value');
        } else {
            $exportFields = [];
        }

        if ($updateMode == 'code') {
            if (!in_array('CODE', $exportFields)) {
                $exportFields[] = 'CODE';
            }
        } elseif ($updateMode == 'xml_id') {
            if (!in_array('XML_ID', $exportFields)) {
                $exportFields[] = 'XML_ID';
            }
        }

        return $exportFields;
    }

    /**
     * @throws HelperException
     * @throws RebuildException
     * @return integer
     */
    protected function getFieldValueIblockId()
    {
        $helper = $this->getHelperManager();

        $iblockId = $this->addFieldAndReturn(
            'iblock_id', [
                'title'       => Locale::getMessage('BUILDER_IblockElementsExport_IblockId'),
                'placeholder' => '',
                'width'       => 250,
                'items'       => $this->getHelperManager()->IblockExchange()->getIblocksStructure(),
            ]
        );

        $iblock = $helper->Iblock()->exportIblock($iblockId);
        if (empty($iblock)) {
            $this->rebuildField('iblock_id');
        }

        return (int)$iblockId;
    }

    /**
     * @throws RebuildException
     * @return string
     */
    protected function getFieldValueUpdateMode()
    {
        $updateMode = $this->addFieldAndReturn(
            'update_mode', [
                'title'       => Locale::getMessage('BUILDER_IblockElementsExport_UpdateMode'),
                'placeholder' => '',
                'width'       => 250,
                'select'      => [
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_NotUpdate'),
                        'value' => 'not',
                    ],
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_UpdateByCode'),
                        'value' => 'code',
                    ],
                    [
                        'title' => Locale::getMessage('BUILDER_IblockElementsExport_UpdateByXmlId'),
                        'value' => 'xml_id',
                    ],
                ],
            ]
        );

        return $updateMode;
    }

    protected function explodeString($string, $delimiter = ',')
    {
        $values = explode($delimiter, trim($string));

        $cleaned = [];
        foreach ($values as $value) {
            $value = trim(strval($value));
            if (!empty($value)) {
                $cleaned[] = $value;
            }
        }
        return $cleaned;
    }
}

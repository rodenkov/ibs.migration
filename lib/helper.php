<?php

namespace Sprint\Migration;

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use CDBResult;
use CMain;
use ReflectionClass;
use Sprint\Migration\Exceptions\HelperException;

class Helper
{
    use OutTrait {
        out as protected;
        outIf as protected;
        outProgress as protected;
        outNotice as protected;
        outNoticeIf as protected;
        outInfo as protected;
        outInfoIf as protected;
        outSuccess as protected;
        outSuccessIf as protected;
        outWarning as protected;
        outWarningIf as protected;
        outError as protected;
        outErrorIf as protected;
        outDiff as protected;
        outDiffIf as protected;
    }

    private $mode = [
        'test' => 0,
        'out_equal' => 0,
    ];

    /**
     * Helper constructor.
     *
     * @throws HelperException
     */
    public function __construct()
    {
        if (!$this->isEnabled()) {
            $this->throwException(
                __METHOD__,
                Locale::getMessage(
                    'ERR_HELPER_DISABLED',
                    [
                        '#NAME#' => $this->getHelperName(),
                    ]
                )
            );
        }
    }

    public function getMode($key = false)
    {
        if ($key) {
            return $this->mode[$key] ?? 0;
        } else {
            return $this->mode;
        }
    }

    public function setMode($key, $val = 1)
    {
        if ($key instanceof Helper) {
            $this->mode = $key->getMode();
        } else {
            $val = ($val) ? 1 : 0;
            $this->mode[$key] = $val;
        }
    }

    public function setTestMode($val = 1)
    {
        $this->setMode('test', $val);
    }

    public function isEnabled(): bool
    {
        return true;
    }

    /**
     * @param array $names
     *
     * @return bool
     */
    protected function checkModules(array $names = []): bool
    {
        foreach ($names as $name) {
            try {
                if (!Loader::includeModule($name)) {
                    return false;
                }
            } catch (LoaderException $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param        $method
     * @param        $msg
     * @param string ...$vars
     *
     * @throws HelperException
     */
    protected function throwException($method, $msg, ...$vars)
    {
        $args = func_get_args();
        $method = array_shift($args);
        $msg = call_user_func_array('sprintf', $args);

        throw new HelperException(
            $this->getMethod($method) . ': ' . strip_tags($msg)
        );
    }

    /**
     * @param $method
     *
     * @throws HelperException
     */
    protected function throwApplicationExceptionIfExists($method)
    {
        /* @global $APPLICATION CMain */
        global $APPLICATION;
        if ($APPLICATION->GetException()) {
            $this->throwException(
                $method,
                $APPLICATION->GetException()->GetString()
            );
        }
    }

    protected function getHelperName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    protected function hasDiff($exists, $fields): bool
    {
        return ($exists != $fields);
    }

    /**
     * @param $exists
     * @param $fields
     *
     * @return bool
     */
    protected function hasDiffStrict($exists, $fields): bool
    {
        return ($exists !== $fields);
    }

    /**
     * @param string $method
     * @param array  $fields
     * @param array  $reqKeys
     *
     * @throws HelperException
     */
    protected function checkRequiredKeys(string $method, array $fields, array $reqKeys = [])
    {
        foreach ($reqKeys as $name) {
            if (empty($fields[$name])) {
                $this->throwException(
                    $method,
                    Locale::getMessage(
                        'ERR_EMPTY_REQ_FIELD',
                        [
                            '#NAME#' => $name,
                        ]
                    )
                );
            }
        }
    }

    /**
     * @param CDBResult $dbres
     * @param string    $indexKey
     * @param string    $valueKey
     *
     * @return array
     */
    protected function fetchAll(CDBResult $dbres, string $indexKey = '', string $valueKey = ''): array
    {
        $res = [];

        while ($item = $dbres->Fetch()) {
            if ($valueKey) {
                $value = $item[$valueKey];
            } else {
                $value = $item;
            }

            if ($indexKey) {
                $indexVal = $item[$indexKey];
                $res[$indexVal] = $value;
            } else {
                $res[] = $value;
            }
        }

        return $res;
    }

    private function getMethod($method)
    {
        $path = explode('\\', $method);
        return array_pop($path);
    }
}

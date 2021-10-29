<?php

namespace Sprint\Migration;

use ReflectionClass;
use Sprint\Migration\Exceptions\ExchangeException;
use Sprint\Migration\Exceptions\RestartException;

abstract class ExchangeEntity
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
        outMessages as protected;
    }

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @throws RestartException
     */
    public function restart()
    {
        throw new RestartException();
    }

    public function getRestartParams(): array
    {
        return $this->params;
    }

    public function setRestartParams(array $params = [])
    {
        $this->params = $params;
    }

    public function getResourceFile(string $name): string
    {
        $classInfo = new ReflectionClass($this);
        return dirname($classInfo->getFileName()) . '/' . $classInfo->getShortName() . '_files/' . $name;
    }

    public function getClassName(): string
    {
        return (new ReflectionClass($this))->getShortName();
    }

    /**
     * @param $msg
     *
     * @throws ExchangeException
     */
    public function exitWithMessage($msg)
    {
        throw new ExchangeException($msg);
    }

    /**
     * @param $cond
     * @param $msg
     *
     * @throws ExchangeException
     */
    public function exitIf($cond, $msg)
    {
        if ($cond) {
            throw new ExchangeException($msg);
        }
    }

    /**
     * @param $var
     * @param $msg
     *
     * @throws ExchangeException
     */
    public function exitIfEmpty($var, $msg)
    {
        if (empty($var)) {
            throw new ExchangeException($msg);
        }
    }
}

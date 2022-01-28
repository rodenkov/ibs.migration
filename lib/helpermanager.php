<?php

namespace IBS\Migration;

use IBS\Migration\Exceptions\HelperException;
use IBS\Migration\Helpers\AdminIblockHelper;
use IBS\Migration\Helpers\AgentHelper;
use IBS\Migration\helpers\DeliveryServiceHelper;
use IBS\Migration\Helpers\EventHelper;
use IBS\Migration\Helpers\FormHelper;
use IBS\Migration\Helpers\HlblockExchangeHelper;
use IBS\Migration\Helpers\HlblockHelper;
use IBS\Migration\Helpers\IblockExchangeHelper;
use IBS\Migration\Helpers\IblockHelper;
use IBS\Migration\Helpers\LangHelper;
use IBS\Migration\Helpers\MedialibExchangeHelper;
use IBS\Migration\Helpers\MedialibHelper;
use IBS\Migration\Helpers\OptionHelper;
use IBS\Migration\Helpers\SiteHelper;
use IBS\Migration\Helpers\SqlHelper;
use IBS\Migration\Helpers\UserGroupHelper;
use IBS\Migration\Helpers\UserOptionsHelper;
use IBS\Migration\Helpers\UserTypeEntityHelper;

/**
 * @method IblockHelper             Iblock()
 * @method HlblockHelper            Hlblock()
 * @method AgentHelper              Agent()
 * @method EventHelper              Event()
 * @method LangHelper               Lang()
 * @method SiteHelper               Site()
 * @method UserOptionsHelper        UserOptions()
 * @method UserTypeEntityHelper     UserTypeEntity()
 * @method UserGroupHelper          UserGroup()
 * @method OptionHelper             Option()
 * @method FormHelper               Form()
 * @method DeliveryServiceHelper    DeliveryService()
 * @method SqlHelper                Sql()
 * @method MedialibHelper           Medialib()
 * @method MedialibExchangeHelper   MedialibExchange()
 * @method IblockExchangeHelper     IblockExchange()
 * @method HlblockExchangeHelper    HlblockExchange()
 * @method AdminIblockHelper        AdminIblock()
 */
class HelperManager
{
    private        $cache      = [];
    private static $instance   = null;
    private        $registered = [];

    /**
     * @return HelperManager
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @throws HelperException
     * @return Helper
     */
    public function __call($name, $arguments)
    {
        return $this->callHelper($name);
    }

    public function registerHelper($name, $class)
    {
        $this->registered[$name] = $class;
    }

    /**
     * @param $name
     *
     * @throws HelperException
     * @return Helper
     */
    protected function callHelper($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $helperClass = '\\IBS\\Migration\\Helpers\\' . $name . 'Helper';
        if (class_exists($helperClass)) {
            $this->cache[$name] = new $helperClass;
            return $this->cache[$name];
        }

        if (isset($this->registered[$name])) {
            $helperClass = $this->registered[$name];
            if (class_exists($helperClass)) {
                $this->cache[$name] = new $helperClass;
                return $this->cache[$name];
            }
        }

        throw new HelperException("Helper $name not found");
    }
}

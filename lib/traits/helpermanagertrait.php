<?php

namespace IBS\Migration\Traits;

use IBS\Migration\HelperManager;

trait HelperManagerTrait
{
    public function getHelperManager()
    {
        return HelperManager::getInstance();
    }
}

<?php

namespace IBS\Migration\Helpers;

use IBS\Migration\Helper;
use IBS\Migration\Helpers\Traits\Iblock\IblockElementTrait;
use IBS\Migration\Helpers\Traits\Iblock\IblockFieldTrait;
use IBS\Migration\Helpers\Traits\Iblock\IblockPropertyTrait;
use IBS\Migration\Helpers\Traits\Iblock\IblockSectionTrait;
use IBS\Migration\Helpers\Traits\Iblock\IblockTrait;
use IBS\Migration\Helpers\Traits\Iblock\IblockTypeTrait;

class IblockHelper extends Helper
{
    use IblockPropertyTrait;
    use IblockFieldTrait;
    use IblockElementTrait;
    use IblockSectionTrait;
    use IblockTypeTrait;
    use IblockTrait;

    /**
     * IblockHelper constructor.
     */
    public function isEnabled()
    {
        return $this->checkModules(['iblock']);
    }

}

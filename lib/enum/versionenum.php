<?php

namespace IBS\Migration\Enum;

class VersionEnum
{
    const STATUS_INSTALLED = 'installed';
    const STATUS_NEW       = 'new';
    const STATUS_UNKNOWN   = 'unknown';
    const ACTION_UP        = 'up';
    const ACTION_DOWN      = 'down';
    const CONFIG_DEFAULT   = 'cfg';
    const CONFIG_ARCHIVE   = 'archive';
}

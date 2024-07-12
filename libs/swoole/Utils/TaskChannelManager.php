<?php

namespace Lib\swoole\Utils;

use Swoole\Coroutine\Channel;

class TaskChannelManager
{
    private static $channels = [];

    public static function setChannel($task_id, $chan)
    {
        self::$channels[$task_id] = $chan;
    }

    public static function getChannel($task_id)
    {
        return self::$channels[$task_id] ?? null;
    }

    public static function removeChannel($task_id)
    {
        unset(self::$channels[$task_id]);
    }
}


<?php

declare(strict_types=1);

namespace Firezihai\Crontab;

use Throwable;

/**
 * 定时任务执行事件.
 */
class CrontabExecuted
{
    public function __construct(public Crontab $crontab, public bool $isSuccess, public ?Throwable $throwable = null) {}

    public function getThrowable(): null|Throwable
    {
        return $this->throwable;
    }
}

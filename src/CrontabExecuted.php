<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

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

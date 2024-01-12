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

use Carbon\Carbon;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Swoole\Coroutine;

use function Hyperf\Coroutine\co;

class CrontabStrategy
{
    protected Executor $executor;

    /**
     * CrontabScheduler constructor.
     */
    public function __construct(Executor $executor)
    {
        $this->executor = $executor;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function dispatch(Crontab $crontab)
    {
        co(function () use ($crontab) {
            if ($crontab->getExecuteTime() instanceof Carbon) {
                $wait = $crontab->getExecuteTime()->getTimestamp() - time();
                $wait > 0 && Coroutine::sleep($wait);
                $this->executor->execute($crontab);
            }
        });
    }

    /**
     * 执行一次
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function executeOnce(Crontab $crontab)
    {
        co(function () use ($crontab) {
            $this->executor->execute($crontab);
        });
    }
}

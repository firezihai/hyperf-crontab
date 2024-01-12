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

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Coordinator\Constants;
use Hyperf\Coordinator\CoordinatorManager;
use Hyperf\Crontab\Crontab;
use Hyperf\Crontab\Event\CrontabDispatcherStarted;
use Hyperf\Crontab\Strategy\StrategyInterface;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\ProcessManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Swoole\Server;

class CrontabProcess extends AbstractProcess
{
    public string $name = 'Hyperf Crontab';

    private ConfigInterface $config;

    /**
     * @var Server
     */
    private $server;

    /**
     * @var CrontabScheduler
     */
    private $scheduler;

    /**
     * @var StrategyInterface
     */
    private $strategy;

    /**
     * @var StdoutLoggerInterface
     */
    private $logger;

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(ConfigInterface::class);
        $this->scheduler = $container->get(CrontabScheduler::class);
        $this->strategy = $container->get(CrontabStrategy::class);
        $this->logger = $container->get(StdoutLoggerInterface::class);
    }

    public function bind($server): void
    {
        $this->server = $server;
        parent::bind($server);
    }

    /**
     * 是否自启进程.
     * @param \Swoole\Coroutine\Server|\Swoole\Server $server
     */
    public function isEnable($server): bool
    {
        return (bool) $this->config->get('yqp_crontab.enable', false);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(): void
    {
        $this->event?->dispatch(new CrontabDispatcherStarted());
        while (ProcessManager::isRunning()) {
            if ($this->sleep()) {
                break;
            }
            $crontabs = $this->scheduler->schedule();
            while (! $crontabs->isEmpty()) {
                /**
                 * @var Crontab $crontab
                 */
                $crontab = $crontabs->dequeue();
                $this->strategy->dispatch($crontab);
            }
        }
    }

    /**
     * @return bool whether the server shutdown
     */
    private function sleep(): bool
    {
        $current = date('s', time());
        $sleep = 60 - $current;
        $this->logger->debug('Crontab dispatcher sleep ' . $sleep . 's.');
        if ($sleep > 0) {
            if (CoordinatorManager::until(Constants::WORKER_EXIT)->yield($sleep)) {
                return true;
            }
        }

        return false;
    }
}

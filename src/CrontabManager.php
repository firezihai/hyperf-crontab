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
use Hyperf\Contract\ContainerInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Crontab\LoggerInterface;
use Hyperf\Crontab\Parser;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

use function Hyperf\Support\make;

/**
 * 定时任务管理器
 * Class MineCrontabManage.
 */
class CrontabManager
{
    protected ?PsrLoggerInterface $logger = null;

    protected ConfigInterface $config;

    public function __construct(protected Parser $parser, protected ContainerInterface $container)
    {
        if ($container->has(LoggerInterface::class)) {
            $this->logger = $container->get(LoggerInterface::class);
        } elseif ($container->has(StdoutLoggerInterface::class)) {
            $this->logger = $container->get(StdoutLoggerInterface::class);
        }
        $this->config = $container->get(ConfigInterface::class);
    }

    public function getCrontabList(): array
    {
        $driverClass = $this->config->get('yqp_crontab.driver.class');

        $driver = make($driverClass);
        if (! $driver instanceof CrontabDataInterface) {
            throw new InvalidArgumentException(sprintf('The crontab config %s not implements  CrontabDataInterface', $driverClass));
        }
        $data = $driver->getCrontabList();

        if (empty($data)) {
            return [];
        }
        $last = time();
        $list = [];

        foreach ($data as $item) {
            $crontab = new Crontab();
            $crontab->setCallback($item['callback']);
            $crontab->setType((string) $item['type']);
            $crontab->setEnable(true);
            $crontab->setCrontabId($item['id']);
            $crontab->setName($item['name']);
            $crontab->setParameter($item['params'] ?: '');
            $crontab->setRule($item['rule']);
            $crontab->setSingleton($item['singleton'] == 1 ? true : false);
            // 只锁定10分钟
            if ($item['singleton'] == 1) {
                $crontab->setMutexExpires(600);
            }
            if (! $this->parser->isValid($crontab->getRule())) {
                $this->logger->info('Crontab task [' . $item['name'] . '] rule error, skipping execution');
                continue;
            }

            $time = $this->parser->parse($crontab->getRule(), $last);
            if ($time) {
                foreach ($time as $t) {
                    $list[] = clone $crontab->setExecuteTime($t);
                }
            }
        }
        return $list;
    }
}

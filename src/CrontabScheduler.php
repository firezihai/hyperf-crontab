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

use SplQueue;

class CrontabScheduler
{
    /**
     * MineCrontabManage.
     */
    protected CrontabManager $crontabManager;

    /**
     * \SplQueue.
     */
    protected SplQueue $schedules;

    /**
     * CrontabScheduler constructor.
     */
    public function __construct(CrontabManager $crontabManager)
    {
        $this->schedules = new SplQueue();
        $this->crontabManager = $crontabManager;
    }

    public function schedule(): SplQueue
    {
        foreach ($this->getSchedules() ?? [] as $schedule) {
            $this->schedules->enqueue($schedule);
        }
        return $this->schedules;
    }

    protected function getSchedules(): array
    {
        return $this->crontabManager->getCrontabList();
    }
}

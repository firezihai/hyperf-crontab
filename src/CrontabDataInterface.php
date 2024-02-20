<?php

declare(strict_types=1);

namespace Firezihai\Crontab;

interface CrontabDataInterface
{
    public function getCrontabList(): array;
}

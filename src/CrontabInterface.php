<?php

declare(strict_types=1);

namespace Firezihai\Crontab;

interface CrontabInterface
{
    public function execute(?string $params = null);
}

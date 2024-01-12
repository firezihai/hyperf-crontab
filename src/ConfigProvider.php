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

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for crontab.',
                    'source' => __DIR__ . '/../publish/hyperf_crontab.php',
                    'destination' => BASE_PATH . '/config/autoload/yqp_crontab.php',
                ],
            ],
        ];
    }
}

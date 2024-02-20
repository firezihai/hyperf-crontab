<?php

declare(strict_types=1);

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
                    'destination' => BASE_PATH . '/config/autoload/diy_crontab.php',
                ],
            ],
        ];
    }
}

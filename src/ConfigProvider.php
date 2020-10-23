<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Turing\HyperfEvo;

use Turing\HyperfEvo\Middleware\CoreMiddleware;
use Turing\HyperfEvo\Utils\ServiceClient\ServiceClientFactory;
use Turing\HyperfEvo\Utils\ServiceClient\ServiceClientInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'dependencies' => [
                \Hyperf\HttpServer\CoreMiddleware::class => CoreMiddleware::class,
                ServiceClientInterface::class => ServiceClientFactory::class
            ],
            'exceptions' => [
                'handler' => [
                    'http' => [
                        Exception\Handler\AppExceptionHandler::class
                    ]
                ]
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for evo',
                    'source' => __DIR__ . '/../publish/evo.php',
                    'destination' => BASE_PATH . '/config/autoload/evo.php'
                ]
            ]
        ];
    }
}

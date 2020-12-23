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
namespace Turing\HyperfEvo\Utils\ServiceClient;

use GuzzleHttp\Client;
use Hyperf\Guzzle\HandlerStackFactory;
use Psr\Container\ContainerInterface;

class ServiceClientFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $stack = (new HandlerStackFactory())->create();
        $client = make(Client::class, [
            'config' => [
                'handler' => $stack,
            ],
        ]);
        return make(ServiceClient::class, [
            'client' => $client,
            // 'serviceList' => config('evo.service_client.service_list')
        ]);
    }
}

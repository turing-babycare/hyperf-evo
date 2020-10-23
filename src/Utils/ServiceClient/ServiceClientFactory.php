<?php


namespace Turing\HyperfEvo\Utils\ServiceClient;


use GuzzleHttp\Client;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Guzzle\HandlerStackFactory;
use Psr\Container\ContainerInterface;

class ServiceClientFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $stack = (new HandlerStackFactory())->create();
        $client = make(Client::class, [
            'config' => [
                'handler'=> $stack
            ]
        ]);
        return make(ServiceClient::class, [
            'client' => $client,
            'serviceList' => json_decode(config('evo.service_client.service_list'),true)
        ]);
    }
}
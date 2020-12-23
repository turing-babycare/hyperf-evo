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

interface ServiceClientInterface
{
    /**
     * @return mixed
     */
    public function request(string $serviceName, string $method, string $uri, array $payload = []);

    /**
     * @return mixed
     */
    public function get(string $serviceName, array $payload);

    /**
     * @return mixed
     */
    public function post(string $serviceName, array $payload);

    /**
     * @return mixed
     */
    public function put(string $serviceName, array $payload);

    /**
     * @return mixed
     */
    public function delete(string $serviceName, array $payload);
}

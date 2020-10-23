<?php


namespace Turing\HyperfEvo\Utils\ServiceClient;



interface ServiceClientInterface
{
    /**
     * @param string $serviceName
     * @param string $method
     * @param string $uri
     * @param array $payload
     * @return mixed
     */
    public function request(string $serviceName, string $method, string $uri, array $payload);

    /**
     * @param string $serviceName
     * @param array $payload
     * @return mixed
     */
    public function get(string $serviceName, array $payload);

    /**
     * @param string $serviceName
     * @param array $payload
     * @return mixed
     */
    public function post(string $serviceName, array $payload);

    /**
     * @param string $serviceName
     * @param array $payload
     * @return mixed
     */
    public function put(string $serviceName, array $payload);

    /**
     * @param string $serviceName
     * @param array $payload
     * @return mixed
     */
    public function delete(string $serviceName, array $payload);
}
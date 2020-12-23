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
use GuzzleHttp\Exception\RequestException;
use Hyperf\Utils\Context;
use Psr\Http\Message\ServerRequestInterface;
use Turing\HyperfEvo\Utils\ServiceClient\Exceptions\ServiceClientException;
use Turing\HyperfEvo\Utils\ServiceClient\Exceptions\ServiceClientUnknownException;

class ServiceClient implements ServiceClientInterface
{
    public Client $client;

    public array $serviceList;

    public function __construct(Client $client, array $serviceList = [])
    {
        $this->client = $client;
        $this->serviceList = $serviceList;
    }

    public function getContextTraceId()
    {
        $ctxRequest = Context::get(ServerRequestInterface::class);
        if (empty($ctxRequest)) {
            return '';
        }
        return $ctxRequest->getAttribute('TRACE_ID');
    }

    public function request(string $serviceName, string $method, string $uri, array $payload = [])
    {
        if (! isset($this->serviceList[$serviceName])) {
            throw new ServiceClientUnknownException('远程服务"' . $serviceName . '"未注册');
        }
        $headers = array_merge($payload['headers'] ?? [], ['X_TRACE_ID' => $this->getContextTraceId()]);
        $reqBody = $payload['data'] ?? null;
        $query = $payload['query'] ?? null;
        $requestOptions = [
            'service_name' => $serviceName,
            'method' => $method,
            'url' => $this->serviceList[$serviceName] . $uri,
            'payload' => [
                'headers' => $headers,
                'json' => $reqBody,
                'query' => $query,
            ],
        ];
        try {
            $response = $this->client->request(
                $requestOptions['method'],
                $requestOptions['url'],
                $requestOptions['payload']
            );
            $content = $response->getBody()->getContents();
            return json_decode($content, true);
        } catch (RequestException $e) {
            $request = $e->getRequest();
            $response = $e->getResponse();
            throw new ServiceClientException($e->getMessage(), $serviceName, $request, $response, $requestOptions);
        } catch (\Throwable $e) {
            throw new ServiceClientUnknownException("远端服务\"({$serviceName}){$requestOptions['url']}\"未知错误: {$e->getMessage()}");
        }
    }

    public function get(string $serviceName, array $payload)
    {
        $uri = isset($payload['url']) ? $payload['url'] : '';
        return $this->request($serviceName, 'get', $uri, $payload);
    }

    public function post(string $serviceName, array $payload)
    {
        $uri = isset($payload['url']) ? $payload['url'] : '';
        return $this->request($serviceName, 'post', $uri, $payload);
    }

    public function put(string $serviceName, array $payload)
    {
        $uri = isset($payload['url']) ? $payload['url'] : '';
        return $this->request($serviceName, 'put', $uri, $payload);
    }

    public function delete(string $serviceName, array $payload)
    {
        $uri = isset($payload['url']) ? $payload['url'] : '';
        return $this->request($serviceName, 'delete', $uri, $payload);
    }
}

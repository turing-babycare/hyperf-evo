<?php

declare(strict_types=1);
namespace Turing\HyperfEvo\Utils\ServiceClient;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ServerException;
use Hyperf\Utils\Context;
use Psr\Http\Message\ServerRequestInterface;
use Turing\HyperfEvo\Utils\ServiceClient\Exceptions\ServiceClientBusinessException;
use Turing\HyperfEvo\Utils\ServiceClient\Exceptions\ServiceClientConnectException;
use Turing\HyperfEvo\Utils\ServiceClient\Exceptions\ServiceClientServerException;
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
        return $ctxRequest->getAttribute('TRACE_ID');
    }

    public function request(string $serviceName, string $method, string $uri, array $payload = [])
    {
        if (!isset($this->serviceList[$serviceName])) {
            throw new ServiceClientUnknownException('远程服务"' . $serviceName . '"未注册');
        }
        $url = $this->serviceList[$serviceName] . $uri;
        $headers = array_merge($payload['headers'] ?? [], ['X_TRACE_ID' => $this->getContextTraceId()]);
        $reqBody = $payload['data'] ?? null;
        $query = $payload['query'] ?? null;
        try {
            $respContent = $this->client->request($method, $url, [
                'headers' => $headers,
                'json' => $reqBody,
                'query' => $query
            ])->getBody()->getContents();
            return json_decode($respContent, true);
        } catch (ConnectException $e) {
            throw new ServiceClientConnectException($serviceName, $e->getRequest(), $e->getMessage());
        } catch (ServerException $e) {
            throw new ServiceClientServerException($serviceName, $e->getRequest(), $e->getResponse());
        } catch (ClientException $e) {
            throw new ServiceClientBusinessException($serviceName, $e->getRequest(), $e->getResponse());
        } catch (\Throwable $e) {
            throw new ServiceClientUnknownException("远端服务\"({$serviceName}){$url}\"未知错误:{$e->getMessage()}");
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
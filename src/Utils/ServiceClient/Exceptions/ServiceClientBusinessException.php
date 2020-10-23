<?php


namespace Turing\HyperfEvo\Utils\ServiceClient\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 远程服务业务异常(400错误等)
 * @package App\Utils\ServiceClient\Exceptions
 */
class ServiceClientBusinessException extends ServiceClientException
{
    public function __construct(string $serviceName, RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($serviceName, $request, $response);
        $msg = isset($this->content['message']) ? $this->content['message'] : '未知错误';
        $this->message = $response->getStatusCode() === 400 ? $msg : "远程服务请求错误({$this->getAddress()}) code: {$response->getStatusCode()}, msg: {$msg}";
    }
}
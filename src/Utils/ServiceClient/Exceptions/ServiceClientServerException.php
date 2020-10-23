<?php


namespace Turing\HyperfEvo\Utils\ServiceClient\Exceptions;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 远程服务返回异常（500错误等）
 * @package App\Utils\ServiceClient\Exceptions
 */
class ServiceClientServerException extends ServiceClientException
{

    public function __construct(string $serviceName, RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($serviceName, $request, $response);
        $message = isset($this->content['message']) ? $this->content['message'] : '未知错误';
        $this->message = "远程服务暂时不可用({$this->getAddress()}) code: {$response->getStatusCode()}, msg: {$message}";
    }
//    public function __construct(string $serviceName, RequestInterface $request, ResponseInterface $response)
//    {
//        parent::___
//        $this->serviceName = $serviceName;
//        $this->request = $request;
//        $this->response = $response;
//        $this->content = [];
//        if ($response) {
//            $this->responseContent = $request->getBody()->getContents();
//            try {
//                $this->content = json_decode($this->responseContent, true);
//            } catch (\Throwable $e) {
//            }
//        }
//        $message = $this->content['message'] ? $this->content['message'] : '未知错误';
//        parent::__construct("远程服务暂时不可用({$this->getAddress()}), code: {$response->getStatusCode()}, msg: {$message}");
//    }
}
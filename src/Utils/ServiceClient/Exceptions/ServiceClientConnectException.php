<?php


namespace Turing\HyperfEvo\Utils\ServiceClient\Exceptions;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 远程服务连接异常（网络错误等）
 * @package App\Utils\ServiceClient\Exceptions
 */
class ServiceClientConnectException extends ServiceClientException
{
    public function __construct(string $serviceName, RequestInterface $request, $message)
    {
        parent::__construct($serviceName, $request, null);
        $this->message = "远程服务连接故障({$this->getAddress()}): " . $message;
    }
//    public function __construct(string $serviceName, RequestInterface $request, $message)
//    {
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
//        parent::__construct("远程服务连接故障({$this->getAddress()}): " . $message);
//    }
}
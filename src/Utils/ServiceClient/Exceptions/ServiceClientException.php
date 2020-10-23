<?php


namespace Turing\HyperfEvo\Utils\ServiceClient\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class ServiceClientException extends RuntimeException
{
    protected string $responseContent;

    protected array $content;

    public RequestInterface $request;

    public ?ResponseInterface $response;

    public string $serviceName;

    public function __construct(string $serviceName, RequestInterface $request, ResponseInterface $response = null)
    {
        $this->serviceName = $serviceName;
        $this->request = $request;
        $this->response = $response;
        $this->content = [];
        if ($response) {
            $this->responseContent = $response->getBody()->getContents();
            try {
                $this->content = json_decode($this->responseContent, true);
            } catch (\Throwable $e) {
            }
        }
        parent::__construct();
    }

    public function getAddress()
    {
        return "[{$this->request->getMethod()}]({$this->serviceName}){$this->request->getUri()}";
    }
}
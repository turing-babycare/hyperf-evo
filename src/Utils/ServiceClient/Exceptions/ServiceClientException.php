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
namespace Turing\HyperfEvo\Utils\ServiceClient\Exceptions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ServiceClientException extends RuntimeException
{
    public RequestInterface $request;

    public ?ResponseInterface $response;

    public string $serviceName;

    public array $requestOptions;

    public $content;

    protected string $responseContent;

    public function __construct(string $defaultMessage, string $serviceName, RequestInterface $request, ResponseInterface $response = null, $requestOptions)
    {
        $this->serviceName = $serviceName;
        $this->request = $request;
        $this->response = $response;
        $this->requestOptions = $requestOptions;
        $message = $defaultMessage;
        if ($response) {
            $this->content = $response->getBody()->getContents();
            try {
                $this->content = json_decode($this->content, true);
                $message = $this->content['message'];
            } catch (\Throwable $e) {
            }
        }
        parent::__construct($message);
    }
}

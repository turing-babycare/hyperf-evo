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
namespace Turing\HyperfEvo\Exception\Handler;

use Hyperf\Contract\ConfigInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\MethodNotAllowedHttpException;
use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use Turing\HyperfEvo\Exception\BusinessException;
use Turing\HyperfEvo\Utils\ServiceClient\Exceptions\ServiceClientException;

class AppExceptionHandler extends ExceptionHandler
{
    protected LoggerInterface $logger;

    protected \Hyperf\HttpServer\Contract\ResponseInterface $response;

    protected ConfigInterface $config;

    public function __construct(LoggerFactory $loggerFactory, \Hyperf\HttpServer\Contract\ResponseInterface $response, ConfigInterface $config)
    {
        $this->logger = $loggerFactory->get('log', 'default');
        $this->response = $response;
        $this->config = $config;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $request = Context::get(ServerRequestInterface::class);
        $traceId = null;
        $url = null;
        if ($request) {
            $traceId = $request->getAttribute('TRACE_ID');
            $uri = $request->getUri();
            $url = "[{$request->getMethod()}] {$uri->getScheme()}://{$uri->getAuthority()}{$uri->getPath()}?{$uri->getQuery()}";
        }

        $responseBody = [
            'trace_id' => $traceId,
            'service_name' => $this->config->get('app_name'),
            'message' => $throwable->getMessage(),
            'stack' => sprintf("[%s] %s in %s:%s\n%s", get_class($throwable), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine(), $throwable->getTraceAsString()),
            'service_client_detail' => null,
        ];

        if ($throwable instanceof NotFoundHttpException) {
            $responseBody['message'] = '接口不存在.';
            $response = $this->response->json($responseBody)->withStatus(404);
        } elseif ($throwable instanceof MethodNotAllowedHttpException) {
            $responseBody['message'] = '未定义的操作.';
            $response = $this->response->json($responseBody)->withStatus(405);
        } elseif ($throwable instanceof BusinessException) {
            $response = $this->response->json($responseBody)->withStatus(400);
        } elseif ($throwable instanceof ServiceClientException) {
            $statusCode = 500;
            $errorDetail = [];
            if ($throwable->request) {
                $errorDetail['request'] = $throwable->request;
            }

            if ($throwable->response) {
                $statusCode = $throwable->response->getStatusCode();
                $errorDetail['response'] = [
                    'status' => $throwable->response->getStatusCode(),
                    'body' => $throwable->content,
                ];
            }
            $responseBody['service_client_detail'] = $errorDetail;
            $response = $this->response->json($responseBody)->withStatus($statusCode);
        } else {
            $responseBody['message'] = '服务器错误:' . $responseBody['message'];
            $response = $this->response->json($responseBody)->withStatus(500);
        }
        $errLog = sprintf(
            "\nTYPE: %s\nSERVICE_NAME: %s\nURL: %s\nTRACE_ID: %s\nMESSAGE: %s\nSTACK: %s\nSERVICE_CLIENT_DETAIL: %s\n",
            'RESPONSE-ERROR',
            $responseBody['service_name'],
            $url,
            $traceId,
            $throwable->getMessage(),
            sprintf("[%s] %s in %s:%s\n%s", get_class($throwable), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine(), $throwable->getTraceAsString()),
            json_encode($responseBody['service_client_detail'], JSON_PRETTY_PRINT)
        );
        $this->logger->error($errLog);
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}

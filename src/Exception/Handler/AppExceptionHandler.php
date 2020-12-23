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

use Hyperf\Contract\StdoutLoggerInterface;
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

    public function __construct(LoggerFactory $loggerFactory, \Hyperf\HttpServer\Contract\ResponseInterface $response)
    {
        $this->logger = $loggerFactory->get('log', 'default');
        $this->response = $response;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $traceId = Context::get(ServerRequestInterface::class)->getAttribute('TRACE_ID');
        $stack = sprintf('[%s] %s in %s:%s', get_class($throwable), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine());

        $responseBody = [
            'trace_id' => $traceId,
            'service_name' => env('APP_NAME'),
            'message' => $throwable->getMessage(),
            'stack' => $stack
        ];
        $errorDetail = [];
        if ($throwable instanceof NotFoundHttpException) {
            $responseBody['message'] = '接口不存在.';
            $response = $this->response->json($responseBody)->withStatus(404);
        } else if ($throwable instanceof MethodNotAllowedHttpException) {
            $responseBody['message'] = '未定义的操作.';
            $response = $this->response->json($responseBody)->withStatus(405);
        } else if ($throwable instanceof BusinessException) {
            $response = $this->response->json($responseBody)->withStatus(400);
        } else if ($throwable instanceof ServiceClientException) {
            $statusCode = 500;
            if ($throwable->request) {
                $uri = $throwable->request->getUri();
                $errorDetail["request"] = [
                    'method' => $throwable->request->getMethod(),
                    'url' => "{$uri->getScheme()}://{$uri->getAuthority()}{$uri->getPath()}?{$uri->getQuery()}",
                    'headers' => $throwable->request->getHeaders(),
                    'body' => $throwable->request->getBody()->getContents()
                ];
            }
            if ($throwable->response) {
                $statusCode = $throwable->response->getStatusCode();
                $errorDetail["response"] = [
                    'headers' => $throwable->response->getHeaders(),
                    'status' => $throwable->response->getStatusCode(),
                    'body' => $throwable->response->getBody()->getContents()
                ];
            }
            $responseBody['detail'] = $errorDetail;
            $response = $this->response->json($responseBody)->withStatus($statusCode);
        } else {
            $responseBody['message'] = '服务器错误:' . $responseBody['message'];
            $response = $this->response->json($responseBody)->withStatus(500);
        }

        $split = env('APP_ENV') === 'dev' ? "\n" : ' ';
        $errDetailStr = json_encode($errorDetail, env('APP_ENV') === 'dev' ? JSON_PRETTY_PRINT : null);
        $this->logger->error("[RESPONSE-ERROR]{$split}SERVICE_NAME: {$responseBody['service_name']}{$split}TRACE_ID: {$traceId}{$split}message: {$throwable->getMessage()}{$split}stack: {$throwable}{$split}detail: {$errDetailStr}");

        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}

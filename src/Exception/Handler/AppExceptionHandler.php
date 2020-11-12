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
use Throwable;
use Turing\HyperfEvo\Exception\BusinessException;
use Turing\HyperfEvo\Utils\ServiceClient\Exceptions\ServiceClientException;

class AppExceptionHandler extends ExceptionHandler
{
    protected LoggerFactory $logger;
    protected \Hyperf\HttpServer\Contract\ResponseInterface $response;

    public function __construct(LoggerFactory $logger, \Hyperf\HttpServer\Contract\ResponseInterface $response)
    {
        $this->logger = $logger;
        $this->response = $response;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $traceId = Context::get(ServerRequestInterface::class)->getAttribute('TRACE_ID');
        $stack = sprintf('[%s] %s in %s:%s', get_class($throwable), $throwable->getMessage(), $throwable->getFile(), $throwable->getLine());

        $responseBody = [
            'trace_id' => $traceId,
            'message' => $throwable->getMessage(),
            'stack' => $stack
        ];


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
            if ($throwable->response) {
                $statusCode = $throwable->response->getStatusCode();
            }
            $response = $this->response->json($responseBody)->withStatus($statusCode);
        } else {
            $responseBody['message'] = '服务器错误:' . $responseBody['message'];
            $response = $this->response->json($responseBody)->withStatus(500);
        }


        $this->logger->error($stack);
        return $response;
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}

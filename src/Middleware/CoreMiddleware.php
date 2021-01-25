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
namespace Turing\HyperfEvo\Middleware;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Turing\HyperfEvo\TMQ\ConsumerManager;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    /**
     * @inject
     */
    public ConsumerManager $tcm;

    public function genTraceId(int $length = 10)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($str) - 1;
        $randstr = '';
        for ($i = 0; $i < $length; ++$i) {
            $num = mt_rand(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $traceId = $request->getHeaderLine('X-TRACE-ID') ? $request->getHeaderLine('X-TRACE-ID') : $this->genTraceId(15);
        $request = $request->withAttribute('TRACE_ID', $traceId);
        Context::set(ServerRequestInterface::class, $request);
        $response = Context::get(ResponseInterface::class);
        $response = $response->withAddedHeader('X-TRACE-ID', $traceId);
        Context::set(ResponseInterface::class, $response);

        if ($request->getUri()->getPath() === config('evo.tmq.consume_url')) {
            return $this->handleTmqRequest($request);
        }

        return parent::process($request, $handler);
    }

    public function handleTmqRequest(ServerRequestInterface $request)
    {
        $body = is_array($request->getParsedBody()) ? $request->getParsedBody() : [];
        $query = $request->getQueryParams();
        $payload = array_merge($body, $query);
        $status = 'TOPIC_NOT_FOUND';
        $result = null;
        $consumeWorker = null;
        if (isset($payload['topic'], $payload['body'])) {
            $consumeWorker = $this->tcm->getConsumer($payload['topic']);
        }
        if ($consumeWorker) {
            $status = 'SUCCESS';
            $result = $consumeWorker->handle($payload['body'], $payload);
        }
        return $this->transferToResponse([
            'status' => $status,
            'result' => $result,
        ], $request);
    }
}

<?php

declare(strict_types=1);

namespace Turing\HyperfEvo\Middleware;

use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CoreMiddleware extends \Hyperf\HttpServer\CoreMiddleware
{
    public function genTraceId(int $length = 10)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($str) - 1;
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
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

        return parent::process($request, $handler);
    }

}
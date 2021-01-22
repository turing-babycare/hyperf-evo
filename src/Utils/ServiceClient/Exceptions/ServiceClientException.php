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

use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class ServiceClientException extends RuntimeException
{
    public $request;

    public $response;

    public $content;

    public function __construct($content, $request, ?ResponseInterface $response)
    {
        $this->request = $request;
        $this->content = $content;
        $this->response = $response;
        if ($content && isset($content['message'])) {
            parent::__construct($content['message']);
        } else {
            parent::__construct('未知错误');
        }
    }
}

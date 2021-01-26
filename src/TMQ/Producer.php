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
namespace Turing\HyperfEvo\TMQ;

use Hyperf\Di\Annotation\Inject;
use Turing\HyperfEvo\Exception\BusinessException;
use Turing\HyperfEvo\Utils\ServiceClient\ServiceClientInterface;

class Producer
{
    /**
     * @inject
     */
    public ServiceClientInterface $client;

    public array $consumers = [];

    public function publish(string $topic, $body, $options = [], string $targetService = '')
    {
        $service = $targetService ? $targetService : config('evo.tmq.default_target_service');
        if (! $service) {
            throw new BusinessException('tmq生产消息时必须指定目标接收服务');
        }
        $delay = 0;
        $props = [
            'consume_url' => config('evo.tmq.consume_url'),
            'from_service' => config('evo.tmq.local_service'),
        ];
        if ($options) {
            if (isset($options['delay'])) {
                $delay = $options['delay'];
            }
            if (isset($options['url'])) {
                $props['url'] = $options['url'];
            }
        }
        return $this->client->post(config('evo.tmq.publish_service'), [
            'url' => config('evo.tmq.publish_url'),
            'data' => [
                'body' => $body,
                'target' => $service,
                'delay' => $delay * 1000,
                'topic' => $topic,
                'props' => $props,
            ],
        ]);
    }
}

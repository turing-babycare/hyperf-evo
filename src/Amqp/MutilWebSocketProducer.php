<?php


declare(strict_types=1);

namespace Turing\HyperfEvo\Amqp;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

/**
 * @Producer(exchange="websocket", routingKey="")
 */
class MutilWebSocketProducer extends ProducerMessage
{

    /**
     * WebSocketProducer constructor.
     * @param array $mutis 多个用户 可能不属于同一个命名空间
     * @param string $event 定义的websocket事件
     * @param array $data 数据
     */
    public function __construct(array $mutis,string $event, array $data)
    {
        $this->payload = [
            'mutis' => $mutis,
            'event' => $event,
            'content' => $data,
        ];
    }

}

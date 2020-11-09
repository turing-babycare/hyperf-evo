<?php

declare(strict_types=1);

namespace App\Amqp\Producer;

use Hyperf\Amqp\Annotation\Producer;
use Hyperf\Amqp\Message\ProducerMessage;

/**
 * @Producer(exchange="websocket", routingKey="")
 */
class WebSocketProducer extends ProducerMessage
{

    /**
     * WebSocketProducer constructor.
     * @param string $ns 用户的命名空间 Admin: fhs/admin User: fhs/user
     * @param string $uid 用户的id
     * @param string $event 定义的websocket事件
     * @param array $data 数据
     */
    public function __construct(string $ns,string $uid='',string $event,array $data)
    {
        $this->payload = [
            'ns'=>$ns,
            'uid'=>$uid,
            'event'=>$event,
            'content'=>$data
        ];
    }

}

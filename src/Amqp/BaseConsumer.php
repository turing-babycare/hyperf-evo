<?php

declare(strict_types=1);

namespace Turing\HyperfEvo\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use PhpAmqpLib\Message\AMQPMessage;
use Hyperf\Amqp\Result;

/**
 * @Consumer(exchange="hyperf", routingKey="hyperf", name ="BaseConsumer", nums=1, enable=false)
 */
class BaseConsumer extends ConsumerMessage
{
    public function getQueue(): string
    {
        return $this->exchange.'.'.$this->routingKey;
    }

    public function consumeMessage($data, AMQPMessage $message): string
    {
        print_r($data);

        return Result::ACK;
    }
}

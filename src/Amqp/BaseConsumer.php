<?php
declare(strict_types=1);
namespace Turing\HyperfEvo\Amqp;

use Hyperf\Amqp\Message\ConsumerMessage;

class BaseConsumer extends ConsumerMessage {

    public function getQueue(): string
    {
        return $this->exchange.'.'.$this->routingKey;
    }
}
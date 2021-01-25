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

use App\Utils\TMQ\Annotation\Consumer;
use Hyperf\Di\Annotation\AnnotationCollector;
use Turing\HyperfEvo\Exception\BusinessException;

class ConsumerManager
{
    public array $consumers = [];

    public function getConsumer($topic)
    {
        if (! empty($this->consumers[$topic])) {
            return $this->consumers[$topic];
        }

        $classes = AnnotationCollector::getClassesByAnnotation(Consumer::class);
        foreach ($classes as $consumerClass => $annotation) {
            if ($annotation->topic === $topic) {
                $this->consumers[$topic] = make($consumerClass);
                return $this->consumers[$topic];
            }
        }
        return null;
        // throw new BusinessException('topic: ' . $topic . '对应的TMQConsumer不存在');
    }
}

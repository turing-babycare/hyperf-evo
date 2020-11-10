<?php
declare(strict_types=1);

namespace Turing\HyperfEvo\Traits;

use Hyperf\Snowflake\IdGeneratorInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;

/**
 * Trait UUidTrails
 * https://hyperf.wiki/2.0/#/zh-cn/snowflake
 * @package Turing\HyperfEvo\Traits
 */
trait UUidTrails
{
    /**
     * @inject
     * @var \Hyperf\Snowflake\IdGeneratorInterface
     */
    protected \Hyperf\Snowflake\IdGeneratorInterface $idGenerator;

    /**
     * 获取全局唯一id 基于 https://hyperf.wiki/2.0/#/zh-cn/snowflake
     * @return int
     */
    public function getUuid(){
        return $this->idGenerator->generate();
    }

    /**
     * @param int $id
     * @return \Hyperf\Snowflake\Meta
     */
    public function getMeta(int $id){
        return $this->idGenerator->degenerate($id);
    }
}

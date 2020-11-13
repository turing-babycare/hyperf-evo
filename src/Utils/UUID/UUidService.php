<?php
declare(strict_types=1);

namespace Turing\HyperfEvo\Utils\UUID;
use Hyperf\Di\Annotation\Inject;

class UUidService implements UUidInterface {

    /**
     * @inject
     * @var \Hyperf\Snowflake\IdGeneratorInterface
     */
    private \Hyperf\Snowflake\IdGeneratorInterface $idGenerator;

    public function getUuid():string
    {
        return (string)$this->idGenerator->generate();
    }
}
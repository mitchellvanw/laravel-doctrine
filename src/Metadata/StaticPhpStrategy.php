<?php namespace Mitch\LaravelDoctrine\Metadata;

use Doctrine\ORM\Configuration;
use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;

class StaticPhpStrategy implements MetadataStrategyInterface
{
    /**
     * @type array
     */
    private $paths;

    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * @param \Doctrine\ORM\Configuration $configuration
     *
     * @return void
     */
    public function apply(Configuration $configuration)
    {
        $configuration->setMetadataDriverImpl(new StaticPHPDriver($this->paths));
    }
}

<?php namespace Mitch\LaravelDoctrine\Metadata;

use Doctrine\ORM\Configuration;

interface MetadataStrategyInterface
{
    /**
     * @param \Doctrine\ORM\Configuration $configuration
     *
     * @return void
     */
	public function apply(Configuration $configuration);
}

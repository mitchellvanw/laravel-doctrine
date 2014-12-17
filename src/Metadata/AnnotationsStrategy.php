<?php namespace Mitch\LaravelDoctrine\Metadata;

use Doctrine\ORM\Configuration;

class AnnotationsStrategy implements MetadataStrategyInterface
{
    /**
     * @type array
     */
    private $paths;

    /**
     * @type boolean
     */
    private $useSimpleAnnotations;

    public function __construct(array $paths, $useSimpleAnnotations)
    {
        $this->paths                = $paths;
        $this->useSimpleAnnotations = $useSimpleAnnotations;
    }

    /**
     * @param \Doctrine\ORM\Configuration $configuration
     *
     * @return void
     */
    public function apply(Configuration $configuration)
    {
        $configuration->setMetadataDriverImpl(
            $configuration->newDefaultAnnotationDriver(
                $this->paths, $this->useSimpleAnnotations
            )
        );
    }
}

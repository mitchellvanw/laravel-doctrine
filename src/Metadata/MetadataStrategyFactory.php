<?php namespace Mitch\LaravelDoctrine\Metadata;

class MetadataStrategyFactory
{
    /**
     * @type array
     */
    private $types;

    public function __construct()
    {
        $this->types = [
            'xml'         => [$this, 'xmlStrategy'],
            'yml'         => [$this, 'ymlStrategy'],
            'annotations' => [$this, 'annotationsStrategy'],
            'static_php'  => [$this, 'staticPhpStrategy']
        ];
    }

    /**
     * @param string $type
     * @param callable $factory
     */
    public function addCustomType($type, callable $factory)
    {
        $this->types[$type] = $factory;
    }

    /**
     * @param string $type
     * @param array  $params
     *
     * @return \Mitch\LaravelDoctrine\Metadata\MetadataStrategyInterface
     */
    public function getStrategy($type, array $params)
    {
        if (!array_key_exists($type, $this->types))
        {
            throw new \UnexpectedValueException("Driver $type is not a valid metadata driver. Please choose one of: " . implode(', ', array_keys($this->types)));
        }

        $factoryMethod = $this->types[$type];

        return call_user_func_array($factoryMethod, $params);
    }

    /**
     * @param array $paths
     *
     * @return \Mitch\LaravelDoctrine\Metadata\XmlStrategy
     */
    public function xmlStrategy(array $paths)
    {
        return new XmlStrategy($paths);
    }

    /**
     * @param array $paths
     *
     * @return \Mitch\LaravelDoctrine\Metadata\YmlStrategy
     */
    public function ymlStrategy(array $paths)
    {
        return new YmlStrategy($paths);
    }

    /**
     * @param array $paths
     * @param       $useSimpleAnnotations
     *
     * @return \Mitch\LaravelDoctrine\Metadata\AnnotationsStrategy
     */
    public function annotationsStrategy(array $paths, $useSimpleAnnotations)
    {
        return new AnnotationsStrategy($paths, $useSimpleAnnotations);
    }

    public function staticPhpStrategy(array $paths)
    {
        return new StaticPhpStrategy($paths);
    }
}

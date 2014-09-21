<?php

namespace Mitch\LaravelDoctrine\Console;

use Illuminate\Console\Command;
use Doctrine\ORM\Mapping\ClassMetadataFactory;

abstract class SchemaCommand extends Command
{
    /**
     * Schema tool
     *
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    private $tool;

    /**
     * The class metadata factory
     *
     * @var \Doctrine\ORM\Mapping\ClassMetadataFactory
     */
    protected $metadata;

    public function __construct(ClassMetadataFactory $metadata)
    {
        parent::__construct();

        $this->metadata = $metadata;
    }

    /**
     * Lazy loading of SchemaTool.
     *
     * @return \Doctrine\ORM\Tools\SchemaTool
     */
    protected function getTool()
    {
        if (is_null($this->tool)) {
            $this->tool = $this->getLaravel()->make('Doctrine\ORM\Tools\SchemaTool');
        }

        return $this->tool;
    }
} 

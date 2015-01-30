<?php namespace Mitch\LaravelDoctrine\EventListeners;

use \Doctrine\ORM\Event\LoadClassMetadataEventArgs;

class TablePrefix
{
    protected $prefix = '';

    /**
     * __construct
     *
     * @param string $prefix
     */
    public function __construct($prefix)
    {
        $this->prefix = (string) $prefix;
    }

    /**
     * loadClassMetadata
     *
     * @link http://doctrine-orm.readthedocs.org/en/latest/cookbook/sql-table-prefixes.html
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $classMetadata->setTableName($this->prefix . $classMetadata->getTableName());

        //if we use sequences, also prefix the sequence name
        if($classMetadata->isIdGeneratorSequence()) {
        
            $sequenceDefinition = $classMetadata->sequenceGeneratorDefinition;
            $sequenceDefinition['sequenceName'] = $this->prefix . $sequenceDefinition['sequenceName'];

            $classMetadata->setSequenceGeneratorDefinition($sequenceDefinition);
        }

        foreach ($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if ($mapping['type'] == \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY) {
                $mappedTableName = $classMetadata->associationMappings[$fieldName]['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
            }
        }
    }

}


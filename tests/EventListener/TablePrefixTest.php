<?php namespace Test\EventListeners;

use Mitch\LaravelDoctrine\EventListeners\TablePrefix;
use \Doctrine\ORM\Mapping\ClassMetadata;
use Mockery as m;

class TablePrefixTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->metadata = new ClassMetadata('\Foo');
        $this->metadata->setTableName('foo');
        
        $this->objectManager = m::mock('Doctrine\Common\Persistence\ObjectManager');

        $this->args = new \Doctrine\ORM\Event\LoadClassMetadataEventArgs($this->metadata, $this->objectManager);
    }

    /**
     * Basic prefix test
     */
    public function testPrefixAdded() {

        $tablePrefix = new TablePrefix('someprefix_');

        //call the listener
        $tablePrefix->loadClassMetadata($this->args);

        $this->assertEquals('someprefix_foo', $this->metadata->getTableName());
    }

    /**
     * Oracle specific, autoincrements are done using sequences
     * this tests if the sequence also has the prefix - convinience
     */
    public function testPrefixAddedToSequence() {

        $this->metadata->setSequenceGeneratorDefinition(array('sequenceName' => 'bar'));
        $this->metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_SEQUENCE);

        $tablePrefix = new TablePrefix('someprefix_');
        $tablePrefix->loadClassMetadata($this->args);

        $this->assertEquals('someprefix_foo', $this->metadata->getTableName());
        $this->assertEquals(array('sequenceName' => 'someprefix_bar'), $this->metadata->sequenceGeneratorDefinition);

    }

    public function testManyToManyHasPrefix() {

        $this->metadata->mapManyToMany(array('fieldName' => 'fooBar', 'targetEntity' => 'bar'));

        $tablePrefix = new TablePrefix('someprefix_');
        $tablePrefix->loadClassMetadata($this->args);

        $this->assertEquals('someprefix_foo', $this->metadata->getTableName());
        $this->assertEquals('someprefix_foo_bar', $this->metadata->associationMappings['fooBar']['joinTable']['name']);
    }
}

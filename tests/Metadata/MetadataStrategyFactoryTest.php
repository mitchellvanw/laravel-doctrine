<?php namespace Tests\Metadata;

use Doctrine\ORM\Configuration;
use Mitch\LaravelDoctrine\Metadata\XmlStrategy;
use Mitch\LaravelDoctrine\Metadata\YmlStrategy;
use Mitch\LaravelDoctrine\Metadata\StaticPhpStrategy;
use Mitch\LaravelDoctrine\Metadata\AnnotationsStrategy;
use Mitch\LaravelDoctrine\Metadata\MetadataStrategyFactory;
use Mitch\LaravelDoctrine\Metadata\MetadataStrategyInterface;

class MetadataStrategyFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type MetadataStrategyFactory
     */
    private $msf;

    /**
     * @type array
     */
    private $params;

    public function setUp()
    {
        $this->msf = new MetadataStrategyFactory;
        $this->params = [
            [
                '/some/paths',
                '/to/entities',
                '/or/xml/yml/files',
            ]
        ];
    }

    public function testCreateXmlStrategy()
    {
        $this->assertInstanceOf(
            XmlStrategy::class,
            $this->msf->getStrategy('xml', $this->params)
        );

        $this->assertInstanceOf(
            XmlStrategy::class,
            $this->msf->xmlStrategy($this->params[0])
        );
    }

    public function testCreateYmlStrategy()
    {
        $this->assertInstanceOf(
            YmlStrategy::class,
            $this->msf->getStrategy('yml', $this->params)
        );

        $this->assertInstanceOf(
            YmlStrategy::class,
            $this->msf->ymlStrategy($this->params[0])
        );
    }

    public function testCreateStaticPhpStrategy()
    {
        $this->assertInstanceOf(
            StaticPhpStrategy::class,
            $this->msf->getStrategy('static_php', $this->params)
        );

        $this->assertInstanceOf(
            StaticPhpStrategy::class,
            $this->msf->staticPhpStrategy($this->params[0])
        );
    }

    public function testCreateAnnotationsStrategy()
    {
        $this->assertInstanceOf(
            AnnotationsStrategy::class,
            $this->msf->getStrategy('annotations', [['some/paths', 'to/entities'], true])
        );

        $this->assertInstanceOf(
            AnnotationsStrategy::class,
            $this->msf->getStrategy('annotations', [['some/paths', 'to/entities'], false])
        );

        $this->assertInstanceOf(
            AnnotationsStrategy::class,
            $this->msf->annotationsStrategy(['some/paths', 'to/entities'], true)
        );

        $this->assertInstanceOf(
            AnnotationsStrategy::class,
            $this->msf->annotationsStrategy(['some/paths', 'to/entities'], false)
        );
    }

    public function testFailureToCreateUnknownStrategy()
    {
        $this->setExpectedException(\UnexpectedValueException::class);
        $this->msf->getStrategy('invalid_type', []);
    }

    public function testCreateCustomStrategyWithAClosure()
    {
        $this->msf->addCustomType('my_custom_strategy', function(array $paths, $someString, $aBoolean = false){
            return new ACustomMetadataStrategyForTesting(
                $paths,
                $someString,
                $aBoolean
            );
        });

        $this->assertInstanceOf(
            ACustomMetadataStrategyForTesting::class,
            $strategy = $this->msf->getStrategy('my_custom_strategy', [
                $this->params[0],
                'some_string_value',
                true
            ])
        );

        /** @type ACustomMetadataStrategyForTesting $strategy */
        $this->assertEquals($this->params[0], $strategy->paths);
        $this->assertEquals('some_string_value', $strategy->someString);
        $this->assertEquals(true, $strategy->aBoolean);
    }

    public function testCreateCustomStrategyWithAStaticMethod()
    {
        $this->msf->addCustomType('my_custom_strategy', [ACustomStrategyFactoryForTesting::class, 'staticCreate']);

        $this->assertInstanceOf(
            ACustomMetadataStrategyForTesting::class,
            $strategy = $this->msf->getStrategy('my_custom_strategy', [
                $this->params[0],
                'some_string_value',
                true
            ])
        );

        /** @type ACustomMetadataStrategyForTesting $strategy */
        $this->assertEquals($this->params[0], $strategy->paths);
        $this->assertEquals('some_string_value', $strategy->someString);
        $this->assertEquals(true, $strategy->aBoolean);
    }

    public function testCreateCustomStrategyWithAnInstanceMethod()
    {
        $this->msf->addCustomType('my_custom_strategy', [new ACustomStrategyFactoryForTesting, 'instanceCreate']);

        $this->assertInstanceOf(
            ACustomMetadataStrategyForTesting::class,
            $strategy = $this->msf->getStrategy('my_custom_strategy', [
                $this->params[0],
                'some_string_value',
                true
            ])
        );

        /** @type ACustomMetadataStrategyForTesting $strategy */
        $this->assertEquals($this->params[0], $strategy->paths);
        $this->assertEquals('some_string_value', $strategy->someString);
        $this->assertEquals(true, $strategy->aBoolean);
    }
}

class ACustomMetadataStrategyForTesting implements MetadataStrategyInterface {
    public $paths;
    public $someString;
    public $aBoolean;

    function __construct($paths, $someString, $aBoolean)
    {
        $this->paths      = $paths;
        $this->someString = $someString;
        $this->aBoolean   = $aBoolean;
    }

    function apply(Configuration $configuration) {}
}

class ACustomStrategyFactoryForTesting {
    public function instanceCreate($paths, $someString, $aBoolean)
    {
        return new ACustomMetadataStrategyForTesting(
            $paths,
            $someString,
            $aBoolean
        );
    }

    public static function staticCreate($paths, $someString, $aBoolean)
    {
        return new ACustomMetadataStrategyForTesting(
            $paths,
            $someString,
            $aBoolean
        );
    }
}
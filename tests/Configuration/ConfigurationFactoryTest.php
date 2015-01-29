<?php namespace Tests\Configuration;

use Mitch\LaravelDoctrine\CacheManager;
use Illuminate\Contracts\Config\Repository;
use Mitch\LaravelDoctrine\Metadata\MetadataStrategyFactory;
use Mitch\LaravelDoctrine\Configuration\ConfigurationFactory;
use Mitch\LaravelDoctrine\Metadata\MetadataStrategyInterface;

class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @type \PHPUnit_Framework_MockObject_MockObject */
    private $config;

    /** @type \PHPUnit_Framework_MockObject_MockObject */
    private $cacheManager;

    /** @type \PHPUnit_Framework_MockObject_MockObject */
    private $metadataFactory;

    /** @type ConfigurationFactory */
	private $configurationFactory;

    public function setUp()
    {
        $this->cacheManager = $this->getMockBuilder(CacheManager::class)->disableOriginalConstructor()->getMock();
        $this->config = $this->getMock(Repository::class);
        $this->metadataFactory = $this->getMock(MetadataStrategyFactory::class);

        $paths  = ['some/paths'];
        $simpleAnnotations = true;

        $params = [$paths, $simpleAnnotations];

        $this->config
            ->expects($this->atLeastOnce())
            ->method('get')
            ->will($this->returnValueMap([
                ['app.debug', null, true],
                ['doctrine::proxy.directory', null, '/tmp/proxies'],
                ['doctrine::cache_provider', null, 'memcached'],
                ['doctrine::doctrine.metadata.custom_drivers', [], []],
                ['doctrine::doctrine.mappings.driver', 'annotations', 'annotations'],
                ['doctrine::doctrine.mappings.params', $params, $params],
                ['doctrine::doctrine.metadata', null, $paths],
                ['doctrine::doctrine.simple_annotations', null, $simpleAnnotations]
            ]));

        $this->cacheManager
            ->expects($this->once())
            ->method('getCache')
            ->will($this->returnValue(
                $this->getMock('\Doctrine\Common\Cache\Cache')
            ));

        $strategy = $this->getMock(MetadataStrategyInterface::class);
        $strategy->expects($this->once())->method('apply');

        $this->metadataFactory->expects($this->once())
            ->method('getStrategy')
            ->will($this->returnValue($strategy));

        $this->configurationFactory = new ConfigurationFactory(
            $this->metadataFactory,
            $this->config,
            $this->cacheManager
        );
    }

    public function testCreate()
    {
        $this->configurationFactory->create();
    }
}

<?php namespace Tests\Configuration;

use Mitch\LaravelDoctrine\Configuration\SqliteConfigurationMapper;
use Mockery as m;

class SqliteConfigurationMapperTest extends \PHPUnit_Framework_TestCase
{
	private $sqlMapper;

	public function setUp()
	{
		$this->sqlMapper = new SqliteConfigurationMapper;
	}

	public function testAppropriation()
	{
		$this->assertTrue($this->sqlMapper->appropriate(['driver' => 'sqlite']));
		$this->assertFalse($this->sqlMapper->appropriate(['driver' => 'sqlsdfite']));
	}
	
	public function testMapping()
	{
		require(__DIR__.'/../Stubs/AppPath.php');

		$configuration = [
			'driver'   => 'sqlite',
			'database' => 'db',
			'username' => 'somedude',
			'password' => 'not safe',
			'prefix'   => 'mitch_',
			'charset'  => 'whatevs'
		];

		$expected = [
			'driver'   => 'pdo_sqlite',
			'path'     => $configuration['database'],
			'user'     => $configuration['username'],
			'password' => $configuration['password']
		];

		$actual = $this->sqlMapper->map($configuration);

		$this->assertEquals($expected, $actual);
	}
}

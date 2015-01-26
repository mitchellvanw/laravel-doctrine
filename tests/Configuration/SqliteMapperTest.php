<?php namespace Tests\Configuration;

use Illuminate\Support\Facades\Facade;
use Mitch\LaravelDoctrine\Configuration\SqliteMapper;
use Mockery as m;
use Tests\Stubs\ApplicationStub;

class SqliteMapperTest extends \PHPUnit_Framework_TestCase
{
	private $sqlMapper;

	public function setUp()
	{
		$this->sqlMapper = new SqliteMapper;
	}

	public function testAppropriation()
	{
		$this->assertTrue($this->sqlMapper->isAppropriateFor(['driver' => 'sqlite']));
		$this->assertFalse($this->sqlMapper->isAppropriateFor(['driver' => 'sqlsdfite']));
	}
	
	public function testMapping()
	{
		Facade::setFacadeApplication(new ApplicationStub);
		$configuration = [
			'driver'   => 'sqlite',
			'database' => 'db',
			'username' => 'somedude',
			'prefix'   => 'mitch_',
			'charset'  => 'whatevs'
		];

		$expected = [
			'driver'   => 'pdo_sqlite',
			'path'     => $configuration['database'],
			'user'     => $configuration['username'],
            'password' => null
		];
		$actual = $this->sqlMapper->map($configuration);
		$this->assertEquals($expected, $actual);
	}
}

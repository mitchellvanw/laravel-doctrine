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
		$this->assertTrue($this->sqlMapper->isAppropriate(['driver' => 'sqlite']));
		$this->assertFalse($this->sqlMapper->isAppropriate(['driver' => 'sqlsdfite']));
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
			'path'     => 'path/database/db.sqlite',
			'user'     => $configuration['username']
		];
		$actual = $this->sqlMapper->map($configuration);
		$this->assertEquals($expected, $actual);
	}
}

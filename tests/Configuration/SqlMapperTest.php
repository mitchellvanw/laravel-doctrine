<?php namespace Tests\Configuration;

use Mitch\LaravelDoctrine\Configuration\SqlMapper;
use Mockery as m;

class SqlMapperTest extends \PHPUnit_Framework_TestCase
{
	private $sqlMapper;

	public function setUp()
	{
		$this->sqlMapper = new SqlMapper;
	}

	public function testAppropriation()
	{
		$this->assertTrue($this->sqlMapper->isAppropriate(['driver' => 'mysql']));
		$this->assertTrue($this->sqlMapper->isAppropriate(['driver' => 'pgsql']));
		$this->assertTrue($this->sqlMapper->isAppropriate(['driver' => 'sqlsrv']));
	}

	public function testMapping()
	{
		$configuration = [
			'driver'   => 'mysql',
			'host'     => 'localhost',
			'database' => 'db',
			'username' => 'somedude',
			'password' => 'not safe',
			'prefix'   => 'mitch_',
			'charset'  => 'whatevs'
		];
		$expected = [
			'driver'   => 'pdo_mysql',
			'host'     => $configuration['host'],
			'dbname'   => $configuration['database'],
			'user'     => $configuration['username'],
			'password' => $configuration['password'],
			'charset'  => $configuration['charset']
		];
		$actual = $this->sqlMapper->map($configuration);
		$this->assertEquals($expected, $actual);
	}
}

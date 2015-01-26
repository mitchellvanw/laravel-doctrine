<?php namespace Tests\Configuration;

use Mitch\LaravelDoctrine\Configuration\OCIMapper;
use Mockery as m;

class OCIMapperTest extends \PHPUnit_Framework_TestCase
{
	private $sqlMapper;

	public function setUp()
	{
		$this->sqlMapper = new OCIMapper;
	}

	public function testAppropriation()
	{
		$this->assertTrue($this->sqlMapper->isAppropriateFor(['driver' => 'oci8']));
		$this->assertTrue($this->sqlMapper->isAppropriateFor(['driver' => 'pdo_oci']));
	}

	public function testMapping()
	{
		$configuration = [
			'driver'   => 'oracle',
			'host'     => 'localhost',
			'database' => 'db',
			'username' => 'somedude',
			'password' => 'not safe',
			'prefix'   => 'mitch_',
			'charset'  => 'whatevs',
			'servicename' => 'SID'
		];

		$expected = [
			'pooled'   => false,
			'servicename' => 'SID',
            'port' => 1521,
			'driver'   => 'oci8',
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

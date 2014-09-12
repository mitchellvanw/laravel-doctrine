<?php namespace Tests\Configuration;

use Mitch\LaravelDoctrine\Configuration\SqlConfigurationMapper;
use Mockery as m;

class SqlConfigurationMapperTest extends \PHPUnit_Framework_TestCase
{
	public function testAppropriation()
	{
		$sqlMapper = new SqlConfigurationMapper;

		$this->assertTrue($sqlMapper->appropriate(['driver' => 'mysql']));
		$this->assertTrue($sqlMapper->appropriate(['driver' => 'postgresql']));
		$this->assertTrue($sqlMapper->appropriate(['driver' => 'sqlsrv']));
	}
}

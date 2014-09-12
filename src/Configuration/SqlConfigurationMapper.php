<?php namespace Mitch\LaravelDoctrine\Configuration;

class SqlConfigurationMapper implements MappingInterface
{
	/**
	 * Creates the configuration mapping for SQL database engines, including SQL server, MySQL and PostgreSQL.
	 *
	 * @param array $configuration
	 * @return array
	 */
	public function map(array $configuration)
	{
		return [
			'driver'   => $configuration['driver'],
			'host'     => $configuration['host'],
			'dbname'   => $configuration['database'],
			'user'     => $configuration['username'],
			'password' => $configuration['password'],
			'prefix'   => $configuration['prefix'],
			'charset'  => $configuration['charset']
		];
	}
}

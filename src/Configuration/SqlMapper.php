<?php namespace Mitch\LaravelDoctrine\Configuration;

class SqlMapper implements Mapper
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
			'driver' => $this->driver($configuration['driver']),
			'host' => $configuration['host'],
			'dbname' => $configuration['database'],
			'user' => $configuration['username'],
			'password' => $configuration['password'],
			'charset' => $configuration['charset']
		];
	}

	/**
	 * Is suitable for mapping configurations that use a mysql, postgres or sqlserv setup.
	 *
	 * @param array $configuration
	 * @return boolean
	 */
	public function isAppropriateFor(array $configuration)
	{
		return in_array($configuration['driver'], ['sqlsrv', 'mysql', 'pgsql']);
	}

	/**
	 * Maps the Laravel driver syntax to an Sql doctrine format.
	 *
	 * @param $l4Driver
	 * @return string
	 */
	public function driver($l4Driver)
	{
		$doctrineDrivers = ['mysql' => 'pdo_mysql', 'sqlsrv' => 'pdo_sqlsrv', 'pgsql' => 'pdo_pgsql'];

		return $doctrineDrivers[$l4Driver];
	}
}

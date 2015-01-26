<?php namespace Mitch\LaravelDoctrine\Configuration;

class OCIMapper implements Mapper
{
	/**
	 * Creates the configuration mapping for SQL database engines, including SQL server, MySQL and PostgreSQL.
	 *
	 * @param array $configuration
	 * @return array
	 */
	public function map(array $configuration)
	{

        $defaults = ['pooled' => false];
        $optional = ['servicename', 'instancename'];

        foreach ($optional as $opt) {
            if(isset($configuration[$opt]))
                $defaults[$opt] = $configuration[$opt];
        }

		return array_merge($defaults, [
			'driver' => $this->driver($configuration['driver']),
			'host' => $configuration['host'],
			'dbname' => $configuration['database'],
			'user' => $configuration['username'],
			'password' => $configuration['password'],
			'charset' => $configuration['charset']
		]);
	}

	/**
	 * Is suitable for mapping configurations that use a mysql, postgres or sqlserv setup.
	 *
	 * @param array $configuration
	 * @return boolean
	 */
	public function isAppropriateFor(array $configuration)
	{
		return in_array($configuration['driver'], ['oci8', 'pdo_oci']);
	}

	/**
	 * Maps the Laravel driver syntax to an Sql doctrine format.
	 *
	 * @param $l4Driver
	 * @return string
	 */
	public function driver($l4Driver)
	{
		$doctrineDrivers = ['oci8' => 'oci8', 'pdo_oci' => 'pdo_oci'];

		return $doctrineDrivers[$l4Driver];
	}
}

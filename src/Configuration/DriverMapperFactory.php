<?php namespace Mitch\LaravelDoctrine\Configuration;

class DriverMapperFactory
{
	/**
	 * The driver of the database type to be used for the mapping and instantiation.
	 *
	 * @var string
	 */
	private $driver;

	/**
	 * Construct the driver, store locally.
	 *
	 * @param $driver
	 */
	public function __construct($driver)
	{
		$this->driver = $driver;
	}

	/**
	 * Map the Laravel configuration to a configuration driver, return the result.
	 *
	 * @param $configuration
	 * @return array
	 * @throws \Exception
	 */
	public function map($configuration)
	{
		switch ($configuration['driver'])
		{
			case 'sqlite':
				return (new SqliteConfigurationMapper)->map($configuration);
				break;
			case 'mysql':
			case 'pgsql':
			case 'sqlsrv':
				return (new SqlConfigurationMapper)->map($configuration);
				break;
			default:
				throw new \Exception("Driver {$configuration['driver']} unsupported by package at this time.");
		}
	}
}

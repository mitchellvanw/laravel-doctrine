<?php namespace Mitch\LaravelDoctrine\Configuration;

class DriverMapper
{
	/**
	 * The driver of the database type to be used for the mapping and instantiation.
	 *
	 * @var string
	 */
	private $driver;

	/**
	 * An array of mappers that can be cycled through to determine which mapper
	 * is appropriate for a given configuration arrangement.
	 *
	 * @var array
	 */
	private $configurationMappers = [];

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
	 * Register a new driver configuration mapper.
	 *
	 * @param Mapper $mapper
	 */
	public function registerMapper(Mapper $mapper)
	{
		$this->configurationMappers[] = $mapper;
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
		foreach ($this->configurationMappers as $mapper) {
			if ($mapper->appropriate($configuration)) {
				return $mapper->map($configuration);
			}
		}

		throw new \Exception("Driver {$configuration['driver']} unsupported by package at this time.");
	}
}

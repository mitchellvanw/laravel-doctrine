<?php namespace Mitch\LaravelDoctrine\Configuration;

use Exception;

class DriverMapper
{
	/**
	 * An array of mappers that can be cycled through to determine which mapper
	 * is appropriate for a given configuration arrangement.
	 *
	 * @var array
	 */
	private $mappers = [];

	/**
	 * Register a new driver configuration mapper.
	 *
	 * @param Mapper $mapper
	 */
	public function registerMapper(Mapper $mapper)
	{
		$this->mappers[] = $mapper;
	}

	/**
	 * Map the Laravel configuration to a configuration driver, return the result.
	 *
	 * @param $configuration
	 * @return array
	 * @throws Exception
	 */
	public function map($configuration)
	{
		foreach ($this->mappers as $mapper)
			if ($mapper->isAppropriateFor($configuration))
				return $mapper->map($configuration);

		throw new Exception("Driver {$configuration['driver']} unsupported by package at this time.");
	}
}

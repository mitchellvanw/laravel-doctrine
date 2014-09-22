<?php namespace Mitch\LaravelDoctrine\Configuration;

interface Mapper
{
	/**
	 * Handles the mapping of configuration.
	 *
	 * @param array $configuration
	 * @return mixed
	 */
	public function map(array $configuration);

	/**
	 * Determines whether the configuration array is appropriate for the mapper.
	 *
	 * @param array $configuration
	 * @return mixed
	 */
	public function isAppropriateFor(array $configuration);
}

<?php namespace Mitch\LaravelDoctrine\Configuration;

class SqliteMapper implements Mapper
{
	/**
	 * Map the L4 configuration array to a sqlite-friendly doctrine configuration.
	 *
	 * @param array $configuration
	 * @return array
	 */
	public function map(array $configuration)
	{
		$sqliteConfig = [
			'driver' => 'pdo_sqlite',
			'user' => @$configuration['username'],
			'password' => @$configuration['password']
		];
		$this->databaseLocation($configuration, $sqliteConfig);
		return $sqliteConfig;
	}

	/**
	 * Is only suitable for sqlite configuration mapping.
	 *
	 * @param array $configuration
	 * @return bool
	 */
	public function isAppropriateFor(array $configuration)
	{
		return $configuration['driver'] == 'sqlite';
	}

	/**
	 * Determines the location of the database and appends this to the sqlite configuration.
	 *
	 * @param $configuration
	 * @param $sqliteConfig
	 */
	private function databaseLocation($configuration, &$sqliteConfig)
	{
		if ($configuration['database'] == ':memory:')
			$sqliteConfig['memory'] = true;
		else
			$sqliteConfig['path'] = $configuration['database'];
	}
}

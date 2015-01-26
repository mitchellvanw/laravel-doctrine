<?php namespace Mitch\LaravelDoctrine\Configuration;

class SqliteMapper implements Mapper {

    public function map(array $configuration) {
        $sqliteConfig = [
            'driver' => 'pdo_sqlite',
            'user' => @$configuration['username'],
            'password' => @$configuration['password']
        ];
        $this->determineDatabaseLocation($configuration, $sqliteConfig);
        return $sqliteConfig;
    }

    public function isAppropriateFor(array $configuration) {
        return $configuration['driver'] == 'sqlite';
    }

    private function determineDatabaseLocation($configuration, &$sqliteConfig) {
        if ($configuration['database'] == ':memory:')
            $sqliteConfig['memory'] = true;
        else
            $sqliteConfig['path'] = $configuration['database'];
    }
}

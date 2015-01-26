<?php namespace Mitch\LaravelDoctrine\Configuration;

class SqlMapper implements Mapper {

    public function map(array $configuration) {
        return [
            'driver' => $this->mapLaravelToDoctrineDriver($configuration['driver']),
            'host' => $configuration['host'],
            'dbname' => $configuration['database'],
            'user' => $configuration['username'],
            'password' => $configuration['password'],
            'charset' => $configuration['charset']
        ];
    }

    public function isAppropriateFor(array $configuration) {
        return in_array($configuration['driver'], ['sqlsrv', 'mysql', 'pgsql']);
    }
    
    private function mapLaravelToDoctrineDriver($l4Driver) {
        $doctrineDrivers = ['mysql' => 'pdo_mysql', 'sqlsrv' => 'pdo_sqlsrv', 'pgsql' => 'pdo_pgsql'];
        return $doctrineDrivers[$l4Driver];
    }
}

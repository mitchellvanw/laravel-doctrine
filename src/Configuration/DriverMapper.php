<?php namespace Mitch\LaravelDoctrine\Configuration;

use Exception;

class DriverMapper {

    private $mappers = [];

    public function registerMapper(Mapper $mapper) {
        $this->mappers[] = $mapper;
    }

    public function map($configuration) {
        foreach ($this->mappers as $mapper)
            if ($mapper->isAppropriateFor($configuration))
                return $mapper->map($configuration);
        throw new Exception("Driver {$configuration['driver']} unsupported by package at this time.");
    }
}

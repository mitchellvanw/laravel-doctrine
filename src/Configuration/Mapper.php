<?php namespace Mitch\LaravelDoctrine\Configuration;

interface Mapper {

    public function map(array $configuration);
    public function isAppropriateFor(array $configuration);
}

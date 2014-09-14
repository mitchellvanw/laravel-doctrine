<?php namespace Mitch\LaravelDoctrine\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class TrashedFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $metadata, $table)
    {
        if ($this->isSoftDeletable($metadata->rootEntityName))
            return "{$table}.deleted_at IS NULL || CURRENT_TIMESTAMP < {$table}.deleted_at";

        return '';
    }

    private function isSoftDeletable($entity)
    {
        return array_key_exists('Mitch\LaravelDoctrine\Traits\SoftDeletes', class_uses($entity));
    }
}

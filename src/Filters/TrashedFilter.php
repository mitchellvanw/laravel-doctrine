<?php namespace Mitch\LaravelDoctrine\Filters;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class TrashedFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $entity, $table)
    {
        if ($this->isSoftDeletable($entity->rootEntityName)) {
            return "{$table}.deleted_at IS NULL || {$table}.deleted_at <= NOW()";
        }
        return '';
    }

    private function isSoftDeletable($entity)
    {
        return array_key_exists('Mitch\LaravelDoctrine\Traits\SoftDeletes', class_uses($entity));
    }
}

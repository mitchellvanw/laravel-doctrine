<?php namespace Mitch\LaravelDoctrine\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\Mapping\MappingException;

class MappingDescribeCommand extends Command {

    protected $name = 'doctrine:mapping:describe';
    protected $description = 'Display information about mapped objects.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $this->displayEntity($this->argument('entity'), $entityManager);
    }

    protected function getArguments() {
        return [
            ['entity', null, InputArgument::REQUIRED, 'Full or partial name of entity']
        ];
    }

    private function displayEntity($entityName, EntityManagerInterface $entityManager) {
        $table = new Table($this->output);
        $table->setHeaders(['Field', 'Value']);
        $metadata = $this->getClassMetadata($entityName, $entityManager);
        array_map(
            [$table, 'addRow'],
            array_merge(
                [
                    $this->formatField('Name', $metadata->name),
                    $this->formatField('Root entity name', $metadata->rootEntityName),
                    $this->formatField('Custom generator definition', $metadata->customGeneratorDefinition),
                    $this->formatField('Custom repository class', $metadata->customRepositoryClassName),
                    $this->formatField('Mapped super class?', $metadata->isMappedSuperclass),
                    $this->formatField('Embedded class?', $metadata->isEmbeddedClass),
                    $this->formatField('Parent classes', $metadata->parentClasses),
                    $this->formatField('Sub classes', $metadata->subClasses),
                    $this->formatField('Embedded classes', $metadata->subClasses),
                    $this->formatField('Named queries', $metadata->namedQueries),
                    $this->formatField('Named native queries', $metadata->namedNativeQueries),
                    $this->formatField('SQL result set mappings', $metadata->sqlResultSetMappings),
                    $this->formatField('Identifier', $metadata->identifier),
                    $this->formatField('Inheritance type', $metadata->inheritanceType),
                    $this->formatField('Discriminator column', $metadata->discriminatorColumn),
                    $this->formatField('Discriminator value', $metadata->discriminatorValue),
                    $this->formatField('Discriminator map', $metadata->discriminatorMap),
                    $this->formatField('Generator type', $metadata->generatorType),
                    $this->formatField('Table', $metadata->table),
                    $this->formatField('Composite identifier?', $metadata->isIdentifierComposite),
                    $this->formatField('Foreign identifier?', $metadata->containsForeignIdentifier),
                    $this->formatField('Sequence generator definition', $metadata->sequenceGeneratorDefinition),
                    $this->formatField('Table generator definition', $metadata->tableGeneratorDefinition),
                    $this->formatField('Change tracking policy', $metadata->changeTrackingPolicy),
                    $this->formatField('Versioned?', $metadata->isVersioned),
                    $this->formatField('Version field', $metadata->versionField),
                    $this->formatField('Read only?', $metadata->isReadOnly),

                    $this->formatEntityListeners($metadata->entityListeners),
                ],
                [$this->formatField('Association mappings:', '')],
                $this->formatMappings($metadata->associationMappings),
                [$this->formatField('Field mappings:', '')],
                $this->formatMappings($metadata->fieldMappings)
            )
        );
        $table->render();
    }

    private function getMappedEntities(EntityManagerInterface $entityManager) {
        $entityClassNames = $entityManager->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        if ( ! $entityClassNames)
            throw new InvalidArgumentException(
                'You do not have any mapped Doctrine ORM entities according to the current configuration. ' .
                'If you have entities or mapping files you should check your mapping configuration for errors.'
            );
        return $entityClassNames;
    }

    private function getClassMetadata($entityName, EntityManagerInterface $entityManager) {
        try {
            return $entityManager->getClassMetadata($entityName);
        } catch (MappingException $e) {}
        $matches = array_filter(
            $this->getMappedEntities($entityManager),
            function ($mappedEntity) use ($entityName) {
                return preg_match('{'.preg_quote($entityName).'}', $mappedEntity);
            }
        );
        if ( ! $matches)
            throw new InvalidArgumentException("Could not find any mapped Entity classes matching <comment>{$entityName}</comment>.");
        if (count($matches) > 1)
            throw new InvalidArgumentException("Entity name <comment>{$entityName}</comment> is ambigous, possible matches: ".implode(', ', $matches));
        return $entityManager->getClassMetadata(current($matches));
    }

    private function formatValue($value) {
        if ('' === $value)
            return '';

        if (null === $value)
            return '<comment>Null</comment>';

        if (is_bool($value))
            return '<comment>' . ($value ? 'True' : 'False') . '</comment>';

        if (empty($value))
            return '<comment>Empty</comment>';

        if (is_array($value)) {
            if (defined('JSON_UNESCAPED_UNICODE') && defined('JSON_UNESCAPED_SLASHES'))
                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            return json_encode($value);
        }

        if (is_object($value))
            return sprintf('<%s>', get_class($value));

        if (is_scalar($value))
            return $value;

        throw new InvalidArgumentException(sprintf('Do not know how to format value "%s"', print_r($value, true)));
    }

    private function formatField($label, $value) {
        if (null === $value)
            $value = '<comment>None</comment>';
        return [sprintf('<info>%s</info>', $label), $this->formatValue($value)];
    }

    private function formatMappings(array $propertyMappings) {
        $output = [];
        foreach ($propertyMappings as $propertyName => $mapping) {
            $output[] = $this->formatField("  {$propertyName}", '');
            foreach ($mapping as $field => $value)
                $output[] = $this->formatField("    {$field}", $this->formatValue($value));
        }
        return $output;
    }

    private function formatEntityListeners(array $entityListeners) {
        return $this->formatField('Entity listeners', array_map(function ($entityListener) {
            return get_class($entityListener);
        }, $entityListeners));
    }
} 

<?php namespace Mitch\LaravelDoctrine\Configuration;

use Illuminate\Support\Str;
use Doctrine\ORM\Mapping\NamingStrategy;

class LaravelNamingStrategy implements NamingStrategy
{
	/**
	 * @type Str
	 */
	private $str;

	public function __construct(Str $str)
	{
		$this->str = $str;
	}

	/**
	 * Returns a table name for an entity class.
	 *
	 * @param string $className The fully-qualified class name.
	 *
	 * @return string A table name.
	 */
	public function classToTableName($className)
	{
		return $this->str->plural($this->classToFieldName($className));
	}

	/**
	 * Returns a column name for a property.
	 *
	 * @param string      $propertyName A property name.
	 * @param string|null $className    The fully-qualified class name.
	 *
	 * @return string A column name.
	 */
	public function propertyToColumnName($propertyName, $className = null)
	{
		return $this->str->snake($propertyName);
	}

	/**
	 * Returns the default reference column name.
	 *
	 * @return string A column name.
	 */
	public function referenceColumnName()
	{
		return 'id';
	}

	/**
	 * Returns a join column name for a property.
	 *
	 * @param string $propertyName A property name.
	 *
	 * @return string A join column name.
	 */
	public function joinColumnName($propertyName)
	{
		return $this->str->snake($this->str->singular($propertyName)) . '_' . $this->referenceColumnName();
	}

	/**
	 * Returns a join table name.
	 *
	 * @param string      $sourceEntity The source entity.
	 * @param string      $targetEntity The target entity.
	 * @param string|null $propertyName A property name.
	 *
	 * @return string A join table name.
	 */
	public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
	{
		return $this->classToFieldName($sourceEntity) . '_' . $this->classToFieldName($targetEntity);
	}

	/**
	 * Returns the foreign key column name for the given parameters.
	 *
	 * @param string      $entityName           An entity.
	 * @param string|null $referencedColumnName A property.
	 *
	 * @return string A join column name.
	 */
	public function joinKeyColumnName($entityName, $referencedColumnName = null)
	{
		return $this->classToFieldName($entityName) . '_' .
			($referencedColumnName ?: $this->referenceColumnName());
	}

	private function classToFieldName($className)
	{
		return $this->str->snake(class_basename($className));
	}

	/**
	 * Returns a column name for an embedded property.
	 *
	 * @param string $propertyName
	 * @param string $embeddedColumnName
	 *
	 * @return string
	 */
	function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null)
	{
		return $propertyName.'_'.$embeddedColumnName;
	}
}

<?php
namespace Mitch\LaravelDoctrine\Validation;

use App;
use EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Validation\PresenceVerifierInterface;

class DoctrinePresenceVerifier implements PresenceVerifierInterface
{
	protected $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}
	/**
	 * Count the number of objects in a collection having the given value.
	 *
	 * @param  string $collection
	 * @param  string $column
	 * @param  string $value
	 * @param  int $excludeId
	 * @param  string $idColumn
	 * @param  array $extra
	 * @return int
	 */
	public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = array())
	{
		$config = Config::get('doctrine');
		// add the entity namespace to your doctrine config
		// i.e. 'entity_namespace' => 'App\\Entity\\',
        	$namespace = $config['entity_namespace'];

	        $query = 'SELECT COUNT(ent) ';
	        $query .= 'FROM ' . $namespace . $collection . ' ent ';
	        $query .= 'WHERE ent.' . $column . ' = :value ';
	
	        if (!is_null($excludeId) && $excludeId != 'NULL') {
	            $query .= 'AND ent.'.($idColumn ?: 'id').' <> :excludeid ';
	        }
	
	        foreach ($extra as $key => $extraValue) {
	            $query .= 'AND ent.' . $key . ' = :' . $key . ' ';
	        }
	
	        $query = $this->entityManager
	            ->createQuery($query)
	            ->setParameter('value', $value);
	
	        if (!is_null($excludeId) && $excludeId != 'NULL') {
	            $query->setParameter('excludeid', $excludeId);
	        }
	
	        foreach ($extra as $key => $extraValue) {
	            $query->setParameter($key, $extraValue);
	        }
	
	        return $query->getSingleScalarResult();
	}

	/**
	 * Count the number of objects in a collection with the given values.
	 *
	 * @param  string $collection
	 * @param  string $column
	 * @param  array $values
	 * @param  array $extra
	 * @return int
	 */
	public function getMultiCount($collection, $column, array $values, array $extra = array())
	{
		$queryParts = ['SELECT COUNT(*) FROM', $collection, 'WHERE', "$column IN (?)"];

		foreach ($extra as $key => $extraValue) {
			$queryParts[] = "AND $key = ?";
		}

		$query = $this->createQueryFrom($queryParts);
		$query->setParameter(1, implode(',', $values));

		foreach ($extra as $key => $extraValue) {
			$query->setParameter($key + 2, $extraValue);
		}

		return $query->count();
	}

	/**
	 * Creates a new Doctrine native query based on the query parts array.
	 *
	 * @param array $queryParts
	 * @return mixed
	 */
	private function createQueryFrom(array $queryParts = [])
	{
		$rsm = new ResultSetMapping();

		return $this->entityManager->createNativeQuery(implode(' ', $queryParts), $rsm);
	}
}

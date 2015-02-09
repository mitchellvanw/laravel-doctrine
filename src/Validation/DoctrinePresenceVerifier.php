<?php
namespace Mitch\LaravelDoctrine\Validation;

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
		$queryParts = ['SELECT COUNT(*) FROM', $collection, 'WHERE', "$column = ?"];

		if (!is_null($excludeId) && $excludeId != 'NULL')  {
			$queryParts[] = 'AND '.($idColumn ?: 'id').' <> ?';
		}

		foreach ($extra as $key => $extraValue) {
			$queryParts[] = "AND $key = ?";
		}

		$query = $this->createQueryFrom($queryParts);
		$query->setParameter(1, $value);

		if (!is_null($excludeId) && $excludeId != 'NULL')  {
			$query->setParameter(2, $excludeId);
		}

		foreach ($extra as $key => $extraValue) {
			$query->setParameter($key + 3, $extraValue);
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

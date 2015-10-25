<?php namespace Mitch\LaravelDoctrine\Migrations;

use Doctrine\ORM\QueryBuilder;
use Exception;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;

class DoctrineMigrationRepository implements MigrationRepositoryInterface {

    /**
     * The entity manager
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entities;

    /**
     * The schema tool
     *
     * @var \Doctrine\ORM\Tools\SchemaTool
     */
    protected $schema;

    /**
     * The metadata factory
     *
     * @var \Doctrine\ORM\Mapping\ClassMetadataFactory
     */
    protected $metadata;

    /**
     * Create a new database migration repository instance.
     *
     * @param callable $entitiesCallback
     * @param callable $schemaCallback
     * @param callable $metadataCallback
     */
    public function __construct(callable $entitiesCallback, callable $schemaCallback, callable $metadataCallback)
    {
        $this->entitiesCallback = $entitiesCallback;
        $this->schemaCallback = $schemaCallback;
        $this->metadataCallback = $metadataCallback;
    }

    /**
     * Get the ran migrations.
     *
     * @return array
     */
    public function getRan()
    {
        $migrations = $this->query()
            ->getQuery()->getResult();

        $return = [];

        foreach($migrations as $migration) {
            $return[] = $migration['migration'];
        }

        return $return;
    }

    /**
     * Get the last migration batch.
     *
     * @return array
     */
    public function getLast()
    {
        return $this->query()
            ->where('o.batch = :lastBatch')
            ->setParameter('lastBatch', $this->getLastBatchNumber())
            ->orderBy('o.migration', 'desc')->getQuery()->getResult();
    }
    /**
     * Log that a migration was run.
     *
     * @param  string  $file
     * @param  int     $batch
     * @return void
     */
    public function log($file, $batch)
    {
        $migration = new Migration($file, $batch);
        $this->getEntities()->persist($migration);
        $this->getEntities()->flush();
    }
    /**
     * Remove a migration from the log.
     *
     * @param  object  $migration
     * @return void
     */
    public function delete($migration)
    {
        $this->getEntities()->createQueryBuilder()
            ->delete('Mitch\LaravelDoctrine\Migrations\Migration', 'o')
            ->andWhere('o.migration = :migration')
            ->setParameter('migration', $migration->migration)
            ->getQuery()
            ->execute();
    }
    /**
     * Get the next migration batch number.
     *
     * @return int
     */
    public function getNextBatchNumber()
    {
        return $this->getLastBatchNumber() + 1;
    }
    /**
     * Get the last migration batch number.
     *
     * @return int
     */
    public function getLastBatchNumber()
    {
        $result = $this->getEntities()->createQueryBuilder()
            ->select('o, MAX(o.batch) as max_batch')
            ->from('Mitch\LaravelDoctrine\Migrations\Migration', 'o')
            ->getQuery()->getResult()[0]['max_batch'];

        return $result ?: 0;
    }
    /**
     * Create the migration repository data store.
     *
     * @return void
     */
    public function createRepository()
    {
        $this->getSchemaTool()->updateSchema($this->getMetadata()->getAllMetadata());
    }
    /**
     * Determine if the migration repository exists.
     *
     * @return bool
     */
    public function repositoryExists()
    {
        $schema = $this->getEntities()->getConnection()->getSchemaManager();
        $tables = array_filter($schema->listTables(), function($value) {
            return $value->getName() === 'migrations';
        });

        return !empty($tables);
    }
    /**
     * Get a query builder for the migration table.
     *
     * @return QueryBuilder
     */
    protected function query()
    {
        return $this->getEntities()->createQueryBuilder()
              ->select('o')
              ->from('Mitch\LaravelDoctrine\Migrations\Migration', 'o');
    }

    /**
     * Set the information source to gather data.
     *
     * @param  string $name
     * @throws \Exception
     * @return void
     */
    public function setSource($name) {
        // not implemented
    }

    protected function getEntities() {
        if($this->entities == null) {
            $callable = $this->entitiesCallback;
            $this->entities = $callable();
        }
        return $this->entities;
    }

    protected function getSchemaTool() {
        if($this->schema == null) {
            $callable = $this->schemaCallback;
            $this->schema = $callable();
        }
        return $this->schema;
    }

    protected function getMetadata() {
        if($this->metadata == null) {
            $callable = $this->metadataCallback;
            $this->metadata = $callable();
        }
        return $this->metadata;
    }

}
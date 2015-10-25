<?php

namespace Mitch\LaravelDoctrine\Migrations;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="migrations")
 * @ORM\HasLifecycleCallbacks
 */
class Migration {

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    public $migration;

    /**
     * @ORM\Column(type="integer")
     */
    public $batch;

    /**
     * Constructs the Migration.
     *
     * @param $migration
     * @param $batch
     */
    public function __construct($migration, $batch)
    {
        $this->migration = $migration;
        $this->batch = $batch;
    }

}

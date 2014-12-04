<?php  namespace Mitch\LaravelDoctrine\Console;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DqlCommand extends Command {

    protected $name = 'doctrine:dql';
    protected $description = 'Run a DQL query.';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    public function fire() {

    }

    protected function getArguments() {
        return [
            ['dql', null, InputArgument::REQUIRED, 'DQL query.']
        ];
    }

    protected function getOptions() {
        return [
            ['hydrate', null, InputOption::VALUE_OPTIONAL, 'Hydrate type. Available: object, array, scalar, single_scalar, simpleobject']
        ];
    }
} 

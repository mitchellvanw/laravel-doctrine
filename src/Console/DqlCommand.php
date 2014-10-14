<?php  namespace Mitch\LaravelDoctrine\Console;

use Doctrine\Common\Util\Debug;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DqlCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'doctrine:dql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a DQL query.';

    /**
     * The Entity Manager
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct();

        $this->entityManager = $registry->getManager();
    }

    public function fire()
    {

    }

    protected function getArguments()
    {
        return [
            ['dql', null, InputArgument::REQUIRED, 'DQL query.']
        ];
    }

    protected function getOptions()
    {
        return [
            ['hydrate', null, InputOption::VALUE_OPTIONAL, 'Hydrate type. Available: object, array, scalar, single_scalar, simpleobject']
        ];
    }
}

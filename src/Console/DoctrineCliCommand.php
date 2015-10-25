<?php  namespace Mitch\LaravelDoctrine\Console;

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Illuminate\Console\Command;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;


class DoctrineCliCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'doctrine:cli';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run native doctrine command.';

    /**
     * The Entity Manager
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;

        $this->addArgument(
            'doctrineCliCommand',
            InputArgument::REQUIRED,
            'Doctrine cli command.'
        );
    }

    public function fire()
    {
        $consoleRunner = new ConsoleRunner();
        $cliApplication = $consoleRunner->createApplication(
            $consoleRunner->createHelperSet($this->entityManager)
        );

        $doctrineCliInput = new ArrayInput(array(
            'command' => $this->input->getArgument('doctrineCliCommand'),
        ));

        $cliApplication->run($doctrineCliInput, $this->output);
    }
} 

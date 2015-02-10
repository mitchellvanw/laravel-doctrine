<?php namespace Mitch\LaravelDoctrine\Console;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DqlCommand extends Command {

    protected $name = 'doctrine:dql';
    protected $description = 'Run a DQL query.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $hydrationModeName = $this->option('hydrate');
        $hydrationMode = 'Doctrine\ORM\Query::HYDRATE_' . strtoupper(str_replace('-', '_', $hydrationModeName));
        if ( ! defined($hydrationMode))
            throw new \RuntimeException("Hydration mode [{$hydrationModeName}] does not exist. It should be either: object. array, scalar, or single-scalar.");
        $query = $entityManager->createQuery($this->argument('dql'));
        $query->setFirstResult((int) $this->option('first-result'));
        $query->setMaxResults((int) $this->option('max-result'));
        if ($this->input->getOption('sql')) {
            $this->line(Debug::dump($query->getSQL(), 2, true, false));
            return;
        }
        $results = $query->execute(array(), constant($hydrationMode));
        $this->output->writeln(Debug::dump($results, $this->option('depth'), true, false));
    }

    protected function getArguments() {
        return [
            ['dql', null, InputArgument::REQUIRED, 'DQL query.']
        ];
    }

    protected function getOptions() {
        return [
            ['hydrate', null, InputOption::VALUE_REQUIRED, 'Hydrate type. Available: object, array, scalar, single_scalar, simpleobject.', 'object'],
            ['first-result', null, InputOption::VALUE_REQUIRED, 'The first result in the result set.', 0],
            ['max-result', null, InputOption::VALUE_REQUIRED, 'The maximum number of results in the result set.', 20],
            ['depth', null, InputOption::VALUE_REQUIRED, 'Dumping depth of Entity graph.', 7],
            ['sql', null, InputOption::VALUE_NONE, 'Dump generated SQL instead of executing query.'],
        ];
    }
} 

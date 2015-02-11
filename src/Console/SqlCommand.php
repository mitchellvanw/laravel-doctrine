<?php namespace Mitch\LaravelDoctrine\Console;

use Doctrine\Common\Util\Debug;
use LogicException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SqlCommand extends Command {

    protected $name = 'doctrine:sql';
    protected $description = 'Run a SQL query.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $connection = $entityManager->getConnection();
        $sql = $this->argument('sql');
        $depth = $this->option('depth');
        if ( ! is_numeric($depth))
            throw new LogicException("Option 'depth' must contains an integer value");
        $resultSet = stripos($sql, 'select') === 0 ? $connection->fetchAll($sql) : $connection->executeUpdate($sql);
        Debug::dump($resultSet, (int) $depth);
        $message = ob_get_clean();
        $this->line($message);
    }

    protected function getArguments() {
        return [
            ['sql', null, InputArgument::REQUIRED, 'The SQL statement to execute.']
        ];
    }

    protected function getOptions() {
        return [
            ['depth', null, InputOption::VALUE_REQUIRED, 'Dumping depth of result set.', 7],
        ];
    }
} 

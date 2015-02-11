<?php namespace Mitch\LaravelDoctrine\Console;

use Doctrine\Common\Util\Debug;
use Doctrine\DBAL\Driver\PDOConnection;
use InvalidArgumentException;
use PDOException;
use Illuminate\Console\Command;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ImportSqlCommand extends Command {

    protected $name = 'doctrine:import:sql';
    protected $description = 'Import SQL file(s) directly to the database.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $connection = $entityManager->getConnection();
        $files = (array) $this->argument('file');
        foreach ($files as $file) {
            $filePath = realpath($file);
            // Phar compatibility.
            if (false === $filePath)
                $filePath = $file;
            if ( ! file_exists($filePath))
                throw new InvalidArgumentException("SQL file <info>{$filePath}</info> does not exist.");
            elseif ( ! is_readable($filePath))
                throw new InvalidArgumentException("SQL file <info>{$filePath}</info> does not have read permissions.");
            $this->info("Processing file <comment>%s</comment>...");
            $sql = file_get_contents($filePath);

            if ($connection instanceof PDOConnection) {
                // PDO Drivers
                try {
                    $lines = 0;
                    $stmt = $connection->prepare($sql);
                    $stmt->execute();
                    do {
                        // Required due to "MySQL has gone away!" issue
                        $stmt->fetch();
                        $stmt->closeCursor();
                        $lines++;
                    } while ($stmt->nextRowset());
                    $this->info("{$lines} statements executed!".PHP_EOL);
                } catch (PDOException $e) {
                    $this->error('Uh oh! Something went wrong.');
                    throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
                }
            } else {
                // Non-PDO Drivers (ie. OCI8 driver)
                $stmt = $connection->prepare($sql);
                $rs = $stmt->execute();
                if ($rs) {
                    $this->info('OK!'.PHP_EOL);
                } else {
                    $error = $stmt->errorInfo();
                    $this->error('Uh oh! Something went wrong.');
                    throw new RuntimeException($error[2], $error[0]);
                }
                $stmt->closeCursor();
            }
        }
    }

    protected function getArguments() {
        return [
            ['file', null, InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'File path(s) of SQL to be executed.']
        ];
    }
} 

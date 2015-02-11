<?php namespace Mitch\LaravelDoctrine\Console;

use Doctrine\Common\Util\Debug;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class InfoCommand extends Command {

    protected $name = 'doctrine:info';
    protected $description = 'Show basic information about all mapped entities.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        $entityClassNames = $entityManager->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        if ( ! $entityClassNames)
            throw new \Exception(
                'You do not have any mapped Doctrine ORM entities according to the current configuration. '.
                'If you have entities or mapping files you should check your mapping configuration for errors.'
            );
        $this->info("Found <comment>".count($entityClassNames)."</comment> mapped entities:".PHP_EOL);
        $failure = false;
        foreach ($entityClassNames as $entityClassName) {
            try {
                $entityManager->getClassMetadata($entityClassName);
                $this->line("<info>[OK]</info>   {$entityClassName}");
            } catch (MappingException $e) {
                $this->line("<error>[FAIL]</error> {$entityClassName}");
                $this->comment($e->getMessage());
                $this->line('');
                $failure = true;
            }
        }
        return $failure ? 1 : 0;
    }
} 

<?php namespace Mitch\LaravelDoctrine\Console;

use Symfony\Component\Console\Input\InputOption;

class EnsureProductionSettingsCommand extends Command {

    protected $name = 'doctrine:ensure-production-settings';
    protected $description = 'Verify that Doctrine is properly configured for a production environment.';

    public function fire() {
        $entityManager = $this->laravel->make('Doctrine\ORM\EntityManagerInterface');
        try {
            $entityManager->getConfiguration()->ensureProductionSettings();
            if ($this->option('complete'))
                $entityManager->getConnection()->connect();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
        $this->info('Environment is correctly configured for production.');
    }

    protected function getOptions() {
        return [
            ['complete', null, InputOption::VALUE_NONE, 'Also inspect database connection existence.'],
        ];
    }
} 

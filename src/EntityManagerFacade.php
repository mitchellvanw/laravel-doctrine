<?php namespace Mitch\LaravelDoctrine; 

use Illuminate\Support\Facades\Facade;

class EntityManagerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Doctrine\ORM\EntityManager';
    }
} 

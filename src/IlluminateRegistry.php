<?php namespace Mitch\LaravelDoctrine;

use Doctrine\ORM\ORMException;
use Illuminate\Container\Container;
use Doctrine\Common\Persistence\AbstractManagerRegistry;

final class IlluminateRegistry extends AbstractManagerRegistry
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container, array $connections, array $entityManagers, $defaultConnection, $defaultEntityManager)
    {
        $this->container = $container;
        parent::__construct('ORM', $connections, $entityManagers, $defaultConnection, $defaultEntityManager, 'Doctrine\ORM\Proxy\Proxy');
    }

    /**
     * Fetches/creates the given services.
     * A service in this context is connection or a manager instance.
     * @param string $name The name of the service.
     * @return object The instance of the given service.
     */
    protected function getService($name)
    {
        return $this->container->make($name);
    }

    /**
     * Resets the given services.
     * A service in this context is connection or a manager instance.
     * @param string $name The name of the service.
     * @return void
     */
    protected function resetService($name)
    {
        return $this->container->bind($name, null);
    }

    /**
     * Resolves a registered namespace alias to the full namespace.
     * This method looks for the alias in all registered object managers.
     * @param string $alias The alias.
     * @throws ORMException
     * @return string The full namespace.
     */
    public function getAliasNamespace($alias)
    {
        foreach (array_keys($this->getManagers()) as $name) {
            try {
                return $this->getManager($name)->getConfiguration()->getEntityNamespace($alias);
            } catch (ORMException $e) {
            }
        }

        throw ORMException::unknownEntityNamespace($alias);
    }
}

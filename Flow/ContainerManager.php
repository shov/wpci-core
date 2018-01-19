<?php declare(strict_types=1);

namespace Wpci\Core\Flow;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Manage IoC (Symfony container in a fact) creation, manipulation
 */
class ContainerManager
{
    /** @var null|callable */
    protected $initInstructions = null;

    /** @var null|Container */
    protected $container = null;

    /**
     * Accessor to the container
     * @return Container
     */
    public function getContainer(): Container
    {
        if(is_null($this->container)) {
            $this->createContainer();
        }

        return $this->container;
    }

    /**
     * Reset init instructions as callback,
     * which will be called with the container builder as argument
     * @param callable $callback
     */
    public function initInstructions(callable $callback)
    {
        $this->initInstructions = $callback;
    }

    /**
     * Create container, if it's already existing, recreate it TODO: make sure real REcreate
     */
    public function createContainer()
    {
        $this->container = new ContainerBuilder();

        if(!is_null($this->initInstructions)) {
            call_user_func_array($this->initInstructions, [$this->container]);
        }
    }
}
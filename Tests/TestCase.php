<?php declare(strict_types=1);

namespace Wpci\Core\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Wpci\Core\Core;
use Wpci\Core\Facades\ShutdownPromisePool;

/**
 * Extend the PHPUNIT test case
 */
class TestCase extends BaseTestCase
{
    /** @var Core */
    protected $core;

    /**
     * {@inheritdoc}
     */
    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        //Tricky WP existing check
        if(!defined('ABSPATH')) {
            define('ABSPATH', '');
        }
    }

    public function setUp()
    {
        $this->core = null;
        $this->core = new Core();
    }

    public function tearDown()
    {
        ShutdownPromisePool::callAllPromises();
    }

    /**
     * Get instance from IoC container
     * @param $id
     * @return object
     * @throws \Exception
     */
    protected function coreGet($id)
    {
        return $this->core
            ->getContainerManager()
            ->getContainer()
            ->get($id);
    }

    /**
     * Set the instance for a key in IoC container
     * @param $id
     * @param $instance
     */
    protected function coreSetInstance($id, $instance)
    {
        $this->core
            ->getContainerManager()
            ->getContainer()
            ->set($id, $instance);
    }

    /**
     * Make preparing the arguments for key
     * @param $goal : is the key
     * @param Reference[] ...$arguments
     */
    protected function coreIoCPrepareArguments($goal, ...$arguments)
    {
        /** @var ContainerBuilder $cb */
        $cb = $this->core
            ->getContainerManager()
            ->getContainer();

        $cb->getDefinition($goal)
            ->setArguments($arguments)
            ->setPublic(true);
    }

    /**
     * Compile current IoC container
     */
    protected function compileContainer()
    {
        $this->core
            ->getContainerManager()
            ->getContainer()
            ->compile();
    }
}
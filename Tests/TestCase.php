<?php declare(strict_types=1);

namespace Wpci\Core\Tests;

use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase as BaseTestCase;
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
     * Get instance from IoC container
     * @param $id
     * @param array $params
     * @return object
     */
    protected function coreMake($id, array $params = [])
    {
        return $this->core
            ->getContainerManager()
            ->getContainer()
            ->make($id, $params);
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
            ->instance($id, $instance);
    }

    /**
     * Make preparing the arguments for key
     * @param $goal : is the key
     * @param array $arguments
     */
    protected function coreIoCPrepareArguments($goal, array $arguments)
    {
        /** @var Container $ioc */
        $ioc = $this->core
            ->getContainerManager()
            ->getContainer();

        foreach ($arguments as $shouldBeResolved => $byReference) {
            $ioc->when($goal)
                ->needs($shouldBeResolved)
                ->give($byReference);
        }
    }
}
<?php declare(strict_types=1);

namespace Wpci\Core\Facades;

use Symfony\Component\DependencyInjection\Container;
use Wpci\Core\Helpers\Facade;

/**
 * Class App
 * @package Wpci\Core\Facades
 *
 * @method static Container getContainer()
 * @method static null|mixed getEnvVar(string $var)
 */
class App extends Facade
{
    /**
     * Get entity from container
     * @param $id
     * @return object
     * @throws \Exception
     */
    public static function get($id)
    {
        return static::getFacadeRoot()->getContainer()->get($id);
    }

    /**
     * Return the facade root object
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        global $app;
        return $app;
    }
}
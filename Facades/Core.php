<?php declare(strict_types=1);

namespace Wpci\Core\Facades;

use Symfony\Component\DependencyInjection\Container;
use Wpci\Core\Helpers\AbstractFacade;

/**
 * The facade for @see \Wpci\Core\Core
 *
 * @method static Container getContainer()
 * @method static null|mixed getEnvVar(string $var)
 */
class Core extends AbstractFacade
{
    protected static $core = null;

    /**
     * Get entity from container
     * @param $id
     * @return object
     * @throws \Exception
     * @throws \Error
     */
    public static function get($id)
    {
        return static::getFacadeRoot()->getContainer()->get($id);
    }

    /**
     * Return the facade root object
     * @return mixed
     * @throws \Error
     */
    public static function getFacadeRoot()
    {
        if(is_null(static::$core)) {
            throw new \Error("Have no Core root in Core facade!");
        };

        return static::$core;
    }

    public static function setFacadeRoot(\Wpci\Core\Core $core)
    {
        static::$core = $core;
    }
}
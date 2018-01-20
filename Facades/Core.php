<?php declare(strict_types=1);

namespace Wpci\Core\Facades;

use Wpci\Core\Contracts\AbstractFacade;
use Wpci\Core\Flow\ContainerManager;

/**
 * The facade for @see \Wpci\Core\Core
 *
 * @method static ContainerManager getContainerManager()
 * @method static null|mixed env(string $var, $default = null)
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
        return static::getFacadeRoot()
            ->getContainerManager()
            ->getContainer()
            ->get($id);
    }

    /**
     * Make entity by the container
     * @param $id
     * @param array $params
     * @return object
     * @throws \Error
     */
    public static function make($id, array $params = [])
    {
        return static::getFacadeRoot()
            ->getContainerManager()
            ->getContainer()
            ->make($id, $params);
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
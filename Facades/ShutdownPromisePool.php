<?php declare(strict_types=1);

namespace Wpci\Core\Facades;

use Wpci\Core\Helpers\AbstractFacade;
use Wpci\Core\Helpers\PromisePool;

/**
 * The facade for the Instance (as service) of @see PromisePool
 *
 * @method static addPromise(callable $promise, ?int $priority = null)
 * @method static callAllPromises()
 */
class ShutdownPromisePool extends AbstractFacade
{
    /**
     * Return the facade root object
     * @return mixed
     * @throws \Exception
     * @throws \Error
     */
    public static function getFacadeRoot()
    {
        return Core::get('promise-pool.shutdown');
    }
}
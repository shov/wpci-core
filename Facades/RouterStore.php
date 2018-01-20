<?php declare(strict_types=1);

namespace Wpci\Core\Facades;

use Wpci\Core\Contracts\ActionInterface;
use Wpci\Core\Contracts\RouteConditionInterface;
use Wpci\Core\Contracts\AbstractFacade;

/**
 * The facade for @see \Wpci\Core\Http\RouterStore
 *
 * @method static add(RouteConditionInterface $condition, ActionInterface $action, ?string $key = null)
 * @method static bool removeByKey(string $key)
 * @method static makeBinding()
 */
class RouterStore extends AbstractFacade
{

    /**
     * Return the facade root object
     * @return mixed
     * @throws \Exception
     * @throws \Error
     */
    public static function getFacadeRoot()
    {
        return Core::get('router-store');
    }
}
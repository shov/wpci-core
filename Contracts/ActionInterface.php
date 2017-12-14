<?php declare(strict_types=1);

namespace Wpci\Core\Contracts;

use Wpci\Core\Http\RouterStore;

/**
 * Common interface for the actions (which called by Condition in the route)
 * @see RouteConditionInterface
 * @see RouterStore::makeBinding()
 */
interface ActionInterface
{
    /**
     * Action constructor.
     * @param $reference
     */
    public function __construct($reference);

    /**
     * Call action
     * @param array ...$arguments
     * @return ResponseInterface
     */
    public function call(...$arguments): ResponseInterface;
}
<?php declare(strict_types=1);

namespace Wpci\Core\Http\Drops;

use Wpci\Core\Contracts\ActionInterface;
use Wpci\Core\Contracts\RouteConditionInterface;

/**
 * The condition for wordpress ajax callbacks
 * TODO: Implement it
 */
class WpAjaxCondition implements RouteConditionInterface
{

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function bindWithAction(ActionInterface $action)
    {
        // TODO: Implement bindWithAction() method.
        throw new \Exception("Not implementded");
    }
}
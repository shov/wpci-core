<?php declare(strict_types=1);

namespace Wpci\Core\Helpers;

/**
 * Class PromisePool
 * @package Wpci\Core\Helpers
 */
class PromisePool
{
    protected $pool = [];

    public function addPromise(callable $promise, ?int $priority = null)
    {
        $priority = $priority ?? 0;

        $pool[] = compact('priority', 'promise');
    }

    public function callAllPromises()
    {
        $pool = $this->pool;

        usort($pool, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        foreach ($pool as $promise) {
            $promise();
        }
    }
}
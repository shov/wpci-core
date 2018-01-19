<?php declare(strict_types=1);

namespace Wpci\Core\Flow;

use Wpci\Core\Contracts\PromiseManagerInterface;

/**
 * The pool of callbacks, the main goal of this class
 * is accumulate promises and call all of them at the time
 */
class PromisePool implements PromiseManagerInterface
{
    protected const DEFAULT_PRIORITY = 10;

    /**
     * @var array
     */
    protected $pool = [];

    /**
     * {@inheritdoc}
     */
    public function addPromise(string $name, callable $promise, ?int $priority = null)
    {
        $priority = $priority ?? static::DEFAULT_PRIORITY;

        $this->pool[$name] = compact('priority', 'promise');
    }

    /**
     * {@inheritdoc}
     */
    public function callPromise(string $name)
    {
        $pool = $this->pool;

        if(array_key_exists($name, $pool)) {
            $promise = $pool[$name]['promise'];
            $promise();
        } else {
            throw new \InvalidArgumentException(
                sprintf("There is no promise with given name: %s", $name));
        }
    }

    /**
     * Add no named callback to the pool
     * @param callable $promise
     * @param int|null $priority
     */
    public function addAnonymousPromise(callable $promise, ?int $priority = null)
    {
        $priority = $priority ?? static::DEFAULT_PRIORITY;

        $this->pool[] = compact('priority', 'promise');
    }

    /**
     * Call all callbacks ordered by priority
     */
    public function callAllPromises()
    {
        $pool = $this->pool;

        usort($pool, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        foreach ($pool as $node) {
            $promise = $node['promise'];
            $promise();
        }
    }
}
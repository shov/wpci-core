<?php declare(strict_types=1);

namespace Wpci\Core\Flow;

use Wpci\Core\Contracts\PromiseManagerInterface;
use Wpci\Core\Wordpress\WpProvider;

/**
 * The instance is the proxy to wordpress hook (filters) system
 */
class FilterManager implements PromiseManagerInterface
{
    /** @var WpProvider */
    protected $wpProvider;

    /**
     * DI
     * @param WpProvider $wpProvider
     */
    public function __construct(WpProvider $wpProvider)
    {
        $this->wpProvider = $wpProvider;
    }

    /**
     * {@inheritdoc}
     * @throws \ErrorException
     */
    public function addPromise(string $name, callable $promise, ?int $priority = null)
    {
        $this->wpProvider
            ->addFilter($name, $promise, $priority);
    }

    /**
     * {@inheritdoc}
     * @param mixed $value
     * @throws \ErrorException
     */
    public function callPromise(string $name, $value = null)
    {
        $this->wpProvider
            ->applyFilters($name, $value);
    }
}
<?php declare(strict_types=1);

namespace Wpci\Core\Contracts;

use Wpci\Core\Core;

interface App
{
    /**
     * App constructor. Create application with core
     * @param Core $core
     */
    public function __construct(Core $core);

    /**
     * Make application run
     * @return callable
     */
    public function run();
}
<?php declare(strict_types=1);

namespace Wpci\Core\Contracts;

use Wpci\Core\Core;

interface App
{
    /**
     * Inject core into application
     * @param Core $core
     */
    public function handle(Core $core);
}
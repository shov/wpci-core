<?php declare(strict_types=1);

namespace Wpci\Core\Contracts;

/**
 * Common interface for Responses which returns by @see Action::call
 */
interface ResponseInterface
{
    public function send();

    public function setStatusCode(int $code, $text = null);

    public function setContent($content);
}
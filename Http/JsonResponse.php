<?php declare(strict_types=1);

namespace Wpci\Core\Http;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;
use Wpci\Core\Facades\ShutdownPromisePool;

/**
 * Class JsonResponse
 * @package Wpci\Core\Http
 */
class JsonResponse extends BaseJsonResponse implements \Wpci\Core\Contracts\Response
{
    public function send()
    {
        $result = parent::send();
        ShutdownPromisePool::callAllPromises();
        return $result;
    }
}
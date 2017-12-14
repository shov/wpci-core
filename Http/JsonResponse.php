<?php declare(strict_types=1);

namespace Wpci\Core\Http;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;
use Wpci\Core\Contracts\ResponseInterface;
use Wpci\Core\Facades\ShutdownPromisePool;

/**
 * The response in JSON
 */
class JsonResponse extends BaseJsonResponse implements ResponseInterface
{
    public function send()
    {
        $result = parent::send();
        ShutdownPromisePool::callAllPromises();
        return $result;
    }
}
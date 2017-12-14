<?php declare(strict_types=1);

namespace Wpci\Core\Http;

use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Wpci\Core\Contracts\ResponseInterface;
use Wpci\Core\Facades\ShutdownPromisePool;

/**
 * The regular html/text response
 */
class RegularResponse extends BaseResponse implements ResponseInterface
{
    public function send()
    {
        $result = parent::send();
        ShutdownPromisePool::callAllPromises();
        return $result;
    }
}
<?php declare(strict_types=1);

namespace Wpci\Core\Http;

use Wpci\Core\Contracts\Response;
use Wpci\Core\Facades\Core;

/**
 * Class PagesController
 * @package Wpci\Core\Http
 */
class PagesController extends Responder
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function makeResponse(int $status, $content, ?\Throwable $e = null): Response
    {
        $exceptionMarker = (2 != intval($status / 100));

        if (!is_string($content)) {
            $content = (string)$content;
        }

        if ($exceptionMarker && Core::getEnvVar('testing')) {

            $logData = [$content, $status];
            is_null($e) ?: $logData[] = $e->getTraceAsString();
            Core::get('Logger')->info($logData);

            return new WpResponse($content ?? '', $status);
        }

        if($exceptionMarker) {
            $content = '';
        }

        return new WpResponse($content ?? '', $status);
    }
}
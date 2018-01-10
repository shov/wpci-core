<?php declare(strict_types=1);

namespace Wpci\Core\Http;

use Wpci\Core\Contracts\ResponseInterface;
use Wpci\Core\Facades\Core;

/**
 * Inherit it in your application when will build controller for wordpress site pages
 */
class PagesController extends AbstractResponder
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    protected function makeResponse(int $status, $content, ?\Throwable $e = null): ResponseInterface
    {
        $exceptionMarker = (2 != intval($status / 100));

        if (!is_string($content)) {
            $content = (string)$content;
        }

        if ($exceptionMarker && Core::getEnvVar('TESTING')) {

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
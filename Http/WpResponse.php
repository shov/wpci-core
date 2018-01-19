<?php declare(strict_types=1);

namespace Wpci\Core\Http;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Wpci\Core\Contracts\ResponseInterface;
use Wpci\Core\Facades\Core;
use Wpci\Core\Facades\ShutdownPromisePool;
use Wpci\Core\Wordpress\WpProvider;

/**
 * The response working correctly as regular wordpress page response
 */
class WpResponse extends BaseResponse implements ResponseInterface
{
    /**
     * Dump rendered page as temp file and return its name
     * to including in wordpress template-load (or for something else)
     * Also make the promise to unlink this tmp file after shutdown wp or
     * app stop by exception
     *
     * @return bool|string
     * @throws \Error
     * @throws \ErrorException
     * @throws \Exception
     */
    public function send()
    {
        /** @var WpProvider $wpProvider */
        $wpProvider = Core::get(WpProvider::class);
        $wpProvider->statusHeader($this->getStatusCode());
        $fs = new Filesystem();

        $content = $this->getContent();
        $tmpFilePath = $fs->tempnam(sys_get_temp_dir(), 'wpResponse_');
        $fs->dumpFile($tmpFilePath, $content);

        $unlinkTmpFile = function () use ($tmpFilePath, $fs) {
            $fs->remove($tmpFilePath);
        };

        ShutdownPromisePool::addAnonymousPromise($unlinkTmpFile);

        return $tmpFilePath;
    }
}
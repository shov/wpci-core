<?php declare(strict_types=1);

namespace Wpci\Core\Helpers;
use Symfony\Component\Filesystem\Filesystem;
use Wpci\Core\Facades\ShutdownPromisePool;

/**
 * Useful to search file by reference, or make temp file as well
 */
trait KeyToFileTrait
{
    /**
     * @param string $basePath If $key isn't raw content, will be used to add the path
     * @param string $key May be special reference like "@index" or file path like "/index.php" or just raw file content
     * @param callable $process Will called with filename as argument, it file will be temp (with given content) or real one
     * All temp files will be unlinked after getting the result
     * @param string $ext
     * @return mixed
     * @throws \Exception
     */
    protected function keyToFileForProcess(string $basePath, string $key, callable $process, string $ext)
    {
        $ext = (0 !== strpos($ext, '.')) ? '.' . $ext : $ext;
        $originalKey = $key;

        if ("@" === $key[0]) {
            $key = str_replace(["@", ":"], "/", $key);
        }

        if (!preg_match('/\.' . substr($ext, 1) . '$/', $key)) {
            $key .= $ext;
        }

        $searchPath = $basePath . ((0 === strpos($key, '/')) ? '' : '/') . $key;

        if (!is_readable($searchPath)) {

            $tmpFilePath = tempnam(sys_get_temp_dir(), 'phpKeyToFile_');

            if (false === $tmpFilePath) {
                throw new \Exception("Can't create temp file!");
            }

            file_put_contents($tmpFilePath . $ext, $originalKey);

            $result = $process($tmpFilePath . $ext);

            ShutdownPromisePool::addAnonymousPromise(function() use ($tmpFilePath, $ext){
                (new Filesystem())->remove([$tmpFilePath, $tmpFilePath . $ext]);
            });

            return $result;

        } else {
            return $process($searchPath);
        }
    }
}
<?php declare(strict_types=1);

namespace Wpci\Core\Helpers;
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
     * TODO: maybe have sense give the responsibility for unlink to @see ShutdownPromisePool
     * @return mixed
     * @throws \Exception
     */
    protected function keyToFileForProcess(string $basePath, string $key, callable $process, string $ext)
    {
        $originalKey = $key;

        if ("@" === $key[0]) {
            $key = str_replace(["@", ":"], "/", $key) . $ext;
        }

        if (!is_readable($basePath . $key)) {

            $tmpFilePath = tempnam(sys_get_temp_dir(), 'phpKeyToFile_');

            if (false === $tmpFilePath) {
                throw new \Exception("Can't create temp file!");
            }

            file_put_contents($tmpFilePath . $ext, $originalKey);

            $result = $process($tmpFilePath);

            @unlink($tmpFilePath);
            @unlink($tmpFilePath . $ext);

            return $result;

        } else {
            return $process($basePath . $key);
        }
    }
}
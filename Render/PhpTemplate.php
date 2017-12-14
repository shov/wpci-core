<?php declare(strict_types=1);

namespace Wpci\Core\Render;

use Wpci\Core\Contracts\TemplateInterface;
use Wpci\Core\Facades\Path;
use Wpci\Core\Helpers\KeyToFileTrait;

/**
 * Pure php template engine decorator, the strategy to template with pure php
 */
class PhpTemplate implements TemplateInterface
{
    use KeyToFileTrait;

    const TPL_EXT = '.php';

    /**
     * @inheritdoc
     */
    public function render(string $key, array $data = []): string
    {
        return $this->keyToFileForProcess(Path::getTplPath(), $key, function ($filePath) use ($data) {
            extract($data);

            ob_start();
            include $filePath;
            return ob_get_clean();
        }, static::TPL_EXT);
    }
}
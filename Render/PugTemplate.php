<?php declare(strict_types=1);

namespace Wpci\Core\Render;

use Pug\Pug;
use Wpci\Core\Contracts\TemplateInterface;
use Wpci\Core\Facades\Path;
use Wpci\Core\Helpers\KeyToFileTrait;

/**
 * Pug template engine decorator, the strategy to template with Pug
 */
class PugTemplate implements TemplateInterface
{
    use KeyToFileTrait;

    const TPL_EXT = '.pug';

    protected $engine;

    /**
     * MustacheTemplate constructor.
     */
    public function __construct()
    {
        $options = [
            'extension' => static::TPL_EXT,
        ];

        $this->engine = new Pug();
    }

    /**
     * @inheritdoc
     */
    public function render(string $key, array $data = []): string
    {
        return $this->keyToFileForProcess(Path::getTplPath(), $key, function ($filePath) use ($data) {
            return $this->engine->render($filePath, $data);
        }, static::TPL_EXT);
    }
}
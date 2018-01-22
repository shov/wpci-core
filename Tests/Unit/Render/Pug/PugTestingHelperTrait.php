<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Render\Pug;

use Wpci\Core\Contracts\ResponseInterface;
use Wpci\Core\Contracts\TemplateInterface;
use Wpci\Core\Flow\Path;
use Wpci\Core\Http\RegularResponse;
use Wpci\Core\Render\PugTemplate;
use Wpci\Core\Render\View;
use Wpci\Core\Tests\TestCase;

/**
 * Help test the pug
 * Required: @see TestCase
 * @method coreSetInstance($a, $b)
 * @method coreIoCPrepareArguments($a, $b)
 * @method assertEquals($a, $b)
 * @method assertNotEquals($a, $b)
 */
trait PugTestingHelperTrait
{
    protected function mocking()
    {
        $path = new class (__DIR__, __DIR__) extends Path
        {
            public function getTplPath(string $tail = ''): string
            {
                return __DIR__ . '/tpl';
            }
        };

        $this->coreSetInstance(Path::class, $path);

        $this->coreIoCPrepareArguments(View::class, [
            TemplateInterface::class => PugTemplate::class,
            ResponseInterface::class => RegularResponse::class,
        ]);
    }

    protected function assertEqualsTrimmed($expect, $actual)
    {
        $trimmedExpect = preg_replace('/\s/', '', $expect);
        $trimmedActual = preg_replace('/\s/', '', $actual);

        $this->assertEquals($trimmedExpect, $trimmedActual);
    }

    protected function assertNotEqualsTrimmed($expect, $actual)
    {
        $trimmedExpect = preg_replace('/\s/', '', $expect);
        $trimmedActual = preg_replace('/\s/', '', $actual);

        $this->assertNotEquals($trimmedExpect, $trimmedActual);
    }
}
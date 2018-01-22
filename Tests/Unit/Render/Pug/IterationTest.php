<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Render\Pug;

use Wpci\Core\Http\RegularResponse;
use Wpci\Core\Tests\TestCase;

class IterationTest extends TestCase
{
    use PugTestingHelperTrait;

    /**
     * @test
     */
    public function positive()
    {
        //Arrange
        $this->mocking();

        $tplName = '@iteration::iteration';
        $tplPath = __DIR__ . '/tpl/iteration/iteration.pug';
        $htmlPath = __DIR__ . '/tpl/iteration/iteration.html';

        $this->assertTrue(is_readable($tplPath) && is_readable($htmlPath));
        $expect = file_get_contents($htmlPath);

        //Act
        $response = \Wpci\Core\Facades\View::display($tplName, []);

        //Assert
        /** @var RegularResponse $response */
        $this->assertTrue($response instanceof RegularResponse);

        $content = $response->getContent();

        $this->assertEqualsTrimmed($expect, $content);
    }
}
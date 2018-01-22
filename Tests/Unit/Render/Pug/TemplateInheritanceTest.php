<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Render\Pug;

use Wpci\Core\Http\RegularResponse;
use Wpci\Core\Tests\TestCase;

class TemplateInheritanceTest extends TestCase
{
    use PugTestingHelperTrait;

    /**
     * @test
     */
    public function positiveDefault()
    {
        //Arrange
        $this->mocking();

        $tplName = '@inheritance::layout';
        $tplPath = __DIR__ . '/tpl/inheritance/layout.pug';
        $htmlPath = __DIR__ . '/tpl/inheritance/default.html';

        $this->assertTrue(is_readable($tplPath) && is_readable($htmlPath));
        $expect = file_get_contents($htmlPath);

        //Act
        $response = \Wpci\Core\Facades\View::display($tplName, [
            'title' => 'Inheritance test',
            'pets' => ['cat', 'dog'],
        ]);

        //Assert
        /** @var RegularResponse $response */
        $this->assertTrue($response instanceof RegularResponse);

        $content = $response->getContent();
        $this->assertEqualsTrimmed($expect, $content);
    }

    /**
     * @test
     */
    public function positiveInherit()
    {
        //Arrange
        $this->mocking();

        $tplName = '@inheritance::page_a';
        $tplPath = __DIR__ . '/tpl/inheritance/page_a.pug';
        $htmlPath = __DIR__ . '/tpl/inheritance/page_a.html';

        $this->assertTrue(is_readable($tplPath) && is_readable($htmlPath));
        $expect = file_get_contents($htmlPath);

        //Act
        $response = \Wpci\Core\Facades\View::display($tplName, [
            'title' => 'Inheritance test',
            'pets' => ['cat', 'dog'],
        ]);

        //Assert
        /** @var RegularResponse $response */
        $this->assertTrue($response instanceof RegularResponse);

        $content = $response->getContent();
        $this->assertEqualsTrimmed($expect, $content);
    }
}
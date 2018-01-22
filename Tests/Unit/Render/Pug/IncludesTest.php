<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Render\Pug;

use Wpci\Core\Http\RegularResponse;
use Wpci\Core\Tests\TestCase;

class IncludesTest extends TestCase
{
    use PugTestingHelperTrait;

    /**
     * @test
     */
    public function positive()
    {
        //Arrange
        $this->mocking();

        $tplName = '@includes::host';
        $tplPath = __DIR__ . '/tpl/includes/host.pug';
        $htmlPath = __DIR__ . '/tpl/includes/host.html';

        $this->assertTrue(is_readable($tplPath) && is_readable($htmlPath));
        $expect = file_get_contents($htmlPath);

        //Act
        $response = \Wpci\Core\Facades\View::display($tplName, [
            'title' => 'Includes test',
            'someLabel' => 'Taco tęndy',
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
    public function relativePathWalk()
    {
        //Arrange
        $this->mocking();

        $tplName = '@includes::host_rel';
        $tplPath = __DIR__ . '/tpl/includes/host_rel.pug';
        $htmlPath = __DIR__ . '/tpl/includes/host.html';

        $this->assertTrue(is_readable($tplPath) && is_readable($htmlPath));
        $expect = file_get_contents($htmlPath);

        //Act
        $response = \Wpci\Core\Facades\View::display($tplName, [
            'title' => 'Includes test',
            'someLabel' => 'Taco tęndy',
        ]);

        //Assert
        /** @var RegularResponse $response */
        $this->assertTrue($response instanceof RegularResponse);

        $content = $response->getContent();
        $this->assertEqualsTrimmed($expect, $content);
    }
}
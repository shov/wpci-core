<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Render\Pug;

use Wpci\Core\Http\RegularResponse;
use Wpci\Core\Tests\TestCase;

class BasicPugTest extends TestCase
{
    use PugTestingHelperTrait;

    /**
     * @test
     */
    public function simplePositive()
    {
        //Arrange
        $this->mocking();
        $tplName = 'simplePositive.pug';
        $tplPath = __DIR__ . '/tpl/' . $tplName;
        $htmlPath = __DIR__ . '/tpl/simplePositive.html';

        $this->assertTrue(is_readable($tplPath) && is_readable($htmlPath));
        $expect = file_get_contents($htmlPath);

        //Act
        $response = \Wpci\Core\Facades\View::display($tplName, [
            'pageTitle' => 'Try Pug.php and never recode HTML again',
            'youAreUsingJade' => true
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
    public function simpleNegative()
    {
        //Arrange
        $this->mocking();
        $tplName = 'simplePositive.pug';
        $tplPath = __DIR__ . '/tpl/' . $tplName;
        $htmlPath = __DIR__ . '/tpl/simplePositive.html';

        $this->assertTrue(is_readable($tplPath) && is_readable($htmlPath));
        $expect = file_get_contents($htmlPath);

        //Act
        $response = \Wpci\Core\Facades\View::display($tplName, [
            'pageTitle' => 'Don\'t try Pug never and still recode HTML again and again',
            'youAreUsingJade' => false
        ]);

        //Assert
        /** @var RegularResponse $response */
        $this->assertTrue($response instanceof RegularResponse);

        $content = $response->getContent();

        $this->assertNotEqualsTrimmed($expect, $content);

    }
}
<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Render\Pug;

use Wpci\Core\Http\RegularResponse;
use Wpci\Core\Tests\TestCase;

class JsExpressionTest extends TestCase
{
    use PugTestingHelperTrait;

    /**
     * @test
     */
    public function positiveArrayProtoEmu()
    {
        //Arrange
        $this->mocking();

        $tplName = '@js_expression::array_proto_emu';
        $tplPath = __DIR__ . '/tpl/js_expression/array_proto_emu.pug';
        $htmlPath = __DIR__ . '/tpl/js_expression/array_proto_emu.html';

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

    /**
     * Maybe a bug.. unknown filter ":php"
     */
    public function positiveMethodCall()
    {
        //Arrange
        $this->mocking();

        $tplName = '@js_expression::method_call';
        $tplPath = __DIR__ . '/tpl/js_expression/method_call.pug';
        $htmlPath = __DIR__ . '/tpl/js_expression/method_call.html';

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
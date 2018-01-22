<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Render\Pug;

use Wpci\Core\Http\RegularResponse;
use Wpci\Core\Tests\TestCase;

class MixinsTest extends TestCase
{
    use PugTestingHelperTrait;

    /**
     * @test
     */
    public function positiveSimpleAndBlock()
    {
        //Arrange
        $this->mocking();

        $tplName = '@mixins::mixins';
        $tplPath = __DIR__ . '/tpl/mixins/mixins.pug';
        $htmlPath = __DIR__ . '/tpl/mixins/mixins.html';

        $this->assertTrue(is_readable($tplPath) && is_readable($htmlPath));
        $expect = file_get_contents($htmlPath);

        //Act
        $response = \Wpci\Core\Facades\View::display($tplName, [
            'title' => 'Mixins Test',
        ]);

        //Assert
        /** @var RegularResponse $response */
        $this->assertTrue($response instanceof RegularResponse);

        $content = $response->getContent();

        $this->assertEqualsTrimmed($expect, $content);
    }
}
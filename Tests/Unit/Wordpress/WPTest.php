<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit;

use Wpci\Core\Tests\TestCase;
use Wpci\Core\Wordpress\WP;
use Wpci\Core\Wordpress\WpDb;
use Wpci\Core\Wordpress\WpProvider;

/**
 * @see WP
 */
class WPTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->compileContainer();
    }

    /**
     * @test
     */
    public function getGlobal()
    {
        //Arrange
        $this->mockWP();

        /** @var WpDb $wpdb */
        $wp = $this->coreGet(WP::class);

        //Act
        $wp->register_globals();

        //Assert
        $this->assertTrue(isset($GLOBALS['someglobalhadregistered']));
        $this->assertSame(1, $GLOBALS['someglobalhadregistered']);
    }

    protected function mockWP()
    {
        $globalClassName = '\\WP';
        if (!class_exists($globalClassName)) {

            /** @lang php */
            $php = <<<'CODE'
                class WP {
                   
                    public function register_globals()
                    {
                        $GLOBALS['someglobalhadregistered'] = 1;
                    }
                }
CODE;

            eval($php);

            global $wp;
            $wp = new $globalClassName();
        }

    }
}
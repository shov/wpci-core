<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit;

use Wpci\Core\Tests\TestCase;
use Wpci\Core\Wordpress\WpProvider;

/**
 * @see WpProvider
 */
class WpProviderTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function getWp()
    {
        //Arrange
        $expectedWp = new \stdClass();
        $expectedWp->value = 42.9;

        global $wp;
        $wp = $expectedWp;

        /** @var WpProvider $wpProvider */
        $wpProvider = $this->coreGet(WpProvider::class);

        //Act
        /** @var object $providedWp */
        $providedWp = $wpProvider->getWp();

        //Assert
        $this->assertSame($expectedWp->value, $providedWp->value);
        $this->assertSame(spl_object_hash($expectedWp), spl_object_hash($providedWp));
    }

    /**
     * @test
     */
    public function getWpQuery()
    {
        //Arrange
        $expectedWpQuery = new \stdClass();
        $expectedWpQuery->value = 42.9;

        global $wp_query;
        $wp_query = $expectedWpQuery;

        /** @var WpProvider $wpProvider */
        $wpProvider = $this->coreGet(WpProvider::class);

        //Act
        /** @var object $providedWpQuery */
        $providedWpQuery = $wpProvider->getWpQuery();

        //Assert
        $this->assertSame($expectedWpQuery->value, $providedWpQuery->value);
        $this->assertSame(spl_object_hash($expectedWpQuery), spl_object_hash($providedWpQuery));
    }

    /**
     * @test
     */
    public function getWpPost()
    {
        //Arrange
        $expectedWpPost = new \stdClass();
        $expectedWpPost->value = 42.9;

        global $post;
        $post = $expectedWpPost;

        /** @var WpProvider $wpProvider */
        $wpProvider = $this->coreGet(WpProvider::class);

        //Act
        /** @var object $providedWpPost */
        $providedWpPost = $wpProvider->getWpPost();

        //Assert
        $this->assertSame($expectedWpPost->value, $providedWpPost->value);
        $this->assertSame(spl_object_hash($expectedWpPost), spl_object_hash($providedWpPost));
    }

    /**
     * @test
     */
    public function getWpDb()
    {
        //Arrange
        $expectedWpDb = new \stdClass();
        $expectedWpDb->value = 42.9;

        global $wpdb;
        $wpdb = $expectedWpDb;

        /** @var WpProvider $wpProvider */
        $wpProvider = $this->coreGet(WpProvider::class);

        //Act
        /** @var object $providedWpDb */
        $providedWpDb = $wpProvider->getWpDb();

        //Assert
        $this->assertSame($expectedWpDb->value, $providedWpDb->value);
        $this->assertSame(spl_object_hash($expectedWpDb), spl_object_hash($providedWpDb));
    }

    /**
     * @test
     */
    public function callGlobalFunc()
    {
        //Arrange
        $wpHeadFuncName = 'wp_head';
        if (!function_exists($wpHeadFuncName)) {
            /** @lang php */
            $php = <<<'CODE'
            function wp_head()
            {
                echo "azazaza";
            }
CODE;
            eval($php);
        }

        /** @var WpProvider $wpProvider */
        $wpProvider = $this->coreGet(WpProvider::class);

        //Act
        ob_start();
        $wpProvider->wpHead();

        //Assert
        $this->assertSame('azazaza', ob_get_clean());
    }
}
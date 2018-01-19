<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit;

use Wpci\Core\Tests\TestCase;
use Wpci\Core\Wordpress\WpPost;
use Wpci\Core\Wordpress\WpQuery;

/**
 * @see WpPost
 */
class WpPostTest extends TestCase
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
        $this->mockWpPost();

        $expectedPostId = 69696;

        //Act
        /** @var WpPost $wpPost */
        $wpPost = $this->coreGet(WpPost::class);

        //Assert
        $this->assertSame($expectedPostId, $wpPost->postId);

    }

    /**
     * @test
     */
    public function getNewInstance()
    {
        //Arrange
        $this->mockWpPost();

        $expectedPostId = 13;

        //Act
        /** @var WpPost $wpPost */
        $wpPost = $this->coreGet(WpPost::class);
        $wpPost = $wpPost::get_instance($expectedPostId);

        //Assert
        $this->assertSame($expectedPostId, $wpPost->postId);
    }

    protected function mockWpPost()
    {
        $globalClassName = '\\WP_Post';
        if (!class_exists($globalClassName)) {

            /** @lang php */
            $php = <<<'CODE'
                class WP_Post {
                    public $postId;
                    
                    public static function get_instance($postId)
                    {
                        return new static($postId);
                    }
                    
                    public function __construct($postId = null) {
                        $this->postId = $postId ?? 69696;
                    }
                }
CODE;

            eval($php);

            global $post;
            $post = new $globalClassName();
        }

    }
}
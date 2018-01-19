<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\DataSource;

use Wpci\Core\DataSource\WpciQuery;
use Wpci\Core\Flow\Path;
use Wpci\Core\Tests\TestCase;
use Wpci\Core\Wordpress\WpPost;
use Wpci\Core\Wordpress\WpProvider;
use Wpci\Core\Wordpress\WpQuery;

class WpciQueryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->mockDependencies();
        //$this->compileContainer();
    }

    /**
     * @test
     * @throws \Exception
     * @throws \Error
     */
    public function addPostData()
    {
        //Arrange
        $wpciQuery = new WpciQuery();

        $expectation = [
            'super_field' => 'puper_value_69',
            'title' => 'HelloUnitPestIsAPostTitle',
            'content' => '',
            'something-else' => '<3 the extra',
            'wp-head' => '@@Head@@',
            'wp-footer' => '@@Footer@@',
            'site-url' => 'url',
            'site-wpurl' => 'wpurl',
            'site-description' => 'description',
            'site-rss_url' => 'rss_url',
            'site-rss2_url' => 'rss2_url',
            'site-atom_url' => 'atom_url',
            'site-comments_atom_url' => 'comments_atom_url',
            'site-comments_rss2_url' => 'comments_rss2_url',
            'site-pingback_url' => 'pingback_url',
            'site-stylesheet_url' => 'stylesheet_url',
            'site-stylesheet_directory' => 'stylesheet_directory',
            'site-template_directory' => 'template_directory',
            'site-admin_email' => 'admin_email',
            'site-charset' => 'charset',
            'site-html_type' => 'html_type',
            'site-version' => 'version',
            'site-language' => 'language',
            'site-name' => 'name',
            'A' => [
                [
                    'item' => 'Home11',
                    'link' => 'http://university/11',
                    'active' => true,
                ],
            ],
            'B' => [
                [
                    'item' => 'Home22',
                    'link' => 'http://university/22',
                    'active' => false,
                ],
            ],
            'C' => [
                [
                    'item' => 'Home33',
                    'link' => 'http://university/33',
                    'active' => false,
                ],
            ],
        ];

        //Act
        $data = $wpciQuery->addPostData(function (&$data, $post) {
            $data['something-else'] = $post->somethingElse;
        })
            ->addWpEnv()
            ->addMenu()
            ->fetch();

        //Assert
        $this->assertJsonStringEqualsJsonString(json_encode($expectation), json_encode($data));


    }

    protected function mockDependencies()
    {
        $wpProvider = new class () extends WpProvider
        {
            public function getWpPost()
            {
                return new \stdClass();
            }

            public function getWpQuery()
            {
                return new \stdClass();
            }

            public function getBlogInfo(string $key)
            {
                return $key;
            }

            public function getTheTitle($post = 0)
            {
                return "HelloUnitPestIsAPostTitle";
            }

            public function theContent($moreLinkText = null, $stripTeaser = false)
            {
                return "Lorem ipsum dolor sit amet, the content";
            }

            public function wpHead()
            {
                echo "@@Head@@";
            }

            public function wpFooter()
            {
                echo "@@Footer@@";
            }

            public function wpResetPostData()
            {

            }

            public function wpResetQuery()
            {

            }

            public function getFields($postId = false, $formatValue = true)
            {
                return [
                    'super_field' => 'puper_value_' . $postId, //puper_value_69
                ];
            }

            public function getNavMenuLocations()
            {
                return ['A' => 11, 'B' => 22, 'C' => 33];
            }

            public function wpGetNavMenuItems($menu, $args = [])
            {
                $item = new \stdClass();
                $item->title = 'Home' . $menu;
                $item->url = 'http://university/' . $menu;
                return [
                    $item,
                ];
            }
        };
        $this->coreSetInstance(WpProvider::class, $wpProvider);

        $wpQuery = new class () extends WpQuery
        {
            public $queried_object;

            public function __construct($query = null)
            {
                parent::__construct($query);
                $this->queried_object = new \stdClass();
                $this->queried_object->name = "ByePestUnitIsALoopTitle";
            }

            public function have_posts()
            {
                static $count = 3;
                return 0 < $count--;
            }

            public function the_post()
            {

            }

            public function rewind_posts()
            {

            }

        };
        $this->coreSetInstance(WpQuery::class, $wpQuery);

        $wpPost = new class ($wpProvider) extends WpPost
        {
            public $post_excerpt = 'Lorem, the excerpt...';
            public $ID = 69;
            public $somethingElse = '<3 the extra';
        };
        $this->coreSetInstance(WpPost::class, $wpPost);

        $path = new class() extends Path
        {
            public function __construct(string $root = '', string $core = '')
            {

            }

            public function getCurrentUrl(): string
            {
                return 'http://university/11';
            }
        };
        $this->coreSetInstance(Path::class, $path);
    }
}
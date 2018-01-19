<?php declare(strict_types=1);

namespace Wpci\Core\Wordpress;

use Wpci\Core\Facades\Core;
use Wpci\Core\Helpers\DecoratorTrait;

/**
 * The \WP_Query class proxy
 * @version wp 4.9.1
 *
 * @property $ID;
 * @property $post_author = 0;
 * @property $post_date = '0000-00-00 00:00:00';
 * @property $post_date_gmt = '0000-00-00 00:00:00';
 * @property $post_content = '';
 * @property $post_title = '';
 * @property $post_excerpt = '';
 * @property $post_status = 'publish';
 * @property $comment_status = 'open';
 * @property $ping_status = 'open';
 * @property $post_password = '';
 * @property $post_name = '';
 * @property $to_ping = '';
 * @property $pinged = '';
 * @property $post_modified = '0000-00-00 00:00:00';
 * @property $post_modified_gmt = '0000-00-00 00:00:00';
 * @property $post_content_filtered = '';
 * @property $post_parent = 0;
 * @property $guid = '';
 * @property $menu_order = 0;
 * @property $post_type = 'post';
 * @property $post_mime_type = '';
 * @property $comment_count = 0;
 * @property $filter;
 * @method __isset($key)
 * @method __get($key)
 * @method filter($filter)
 * @method to_array()
 */
class WpPost
{

    use DecoratorTrait;

    protected $wpPost;

    /**
     * @param $postId
     * @return mixed
     * @throws \Error
     * @throws \ErrorException
     * @throws \Exception
     */
    public static function get_instance($postId)
    {
        /** @var WpProvider $wpProvider */
        $wpProvider = Core::get(WpProvider::class);

        $globalPost = $wpProvider->getWpPost();
        return new static($wpProvider, $globalPost::get_instance($postId));
    }

    /**
     * DI, constructor
     * @param WpProvider $wpProvider
     * @param null|\WP_Post $wpPostOriginal
     * @throws \ErrorException
     */
    public function __construct(WpProvider $wpProvider, $wpPostOriginal = null)
    {
        $this->wpPost = $wpPostOriginal ?? $wpProvider->getWpPost();

    }

    /**
     * @return mixed
     */
    protected function getDecoratedObject()
    {
        return $this->wpPost;
    }
}
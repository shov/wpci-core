<?php declare(strict_types=1);

namespace Wpci\Core\Wordpress;

use Wpci\Core\Facades\Core;
use Wpci\Core\Helpers\DecoratorTrait;

/**
 * The \WP_Query class proxy
 * @version wp 4.9.1
 *
 *
 * @property $query;
 * @property $query_vars = array();
 * @property $tax_query;
 * @property $meta_query = false;
 * @property $date_query = false;
 * @property $queried_object;
 * @property $queried_object_id;
 * @property $request;
 * @property $posts;
 * @property $post_count = 0;
 * @property $current_post = -1;
 * @property $in_the_loop = false;
 * @property $post;
 * @property $comments;
 * @property $comment_count = 0;
 * @property $current_comment = -1;
 * @property $comment;
 * @property $found_posts = 0;
 * @property $max_num_pages = 0;
 * @property $max_num_comment_pages = 0;
 * @property $is_single = false;
 * @property $is_preview = false;
 * @property $is_page = false;
 * @property $is_archive = false;
 * @property $is_date = false;
 * @property $is_year = false;
 * @property $is_month = false;
 * @property $is_day = false;
 * @property $is_time = false;
 * @property $is_author = false;
 * @property $is_category = false;
 * @property $is_tag = false;
 * @property $is_tax = false;
 * @property $is_search = false;
 * @property $is_feed = false;
 * @property $is_comment_feed = false;
 * @property $is_trackback = false;
 * @property $is_home = false;
 * @property $is_404 = false;
 * @property $is_embed = false;
 * @property $is_paged = false;
 * @property $is_admin = false;
 * @property $is_attachment = false;
 * @property $is_singular = false;
 * @property $is_robots = false;
 * @property $is_posts_page = false;
 * @property $is_post_type_archive = false;
 * @property $thumbnails_cached = false;
 *
 * @method init()
 * @method parse_query_vars()
 * @method fill_query_vars($array)
 * @method parse_query($query = '')
 * @method parse_tax_query(&$q)
 *
 * @method set_404()
 * @method get($query_var, $default = '')
 * @method set($query_var, $value)
 * @method get_posts()
 *
 * @method next_post()
 * @method the_post()
 * @method have_posts()
 * @method rewind_posts()
 * @method next_comment()
 * @method the_comment()
 * @method have_comments()
 * @method rewind_comments()
 * @method query($query)
 * @method get_queried_object()
 * @method get_queried_object_id()
 *
 * @method __isset($name)
 *
 * @method is_archive()
 * @method is_post_type_archive($post_types = '')
 * @method is_attachment($attachment = '')
 * @method is_author($author = '')
 * @method is_category($category = '')
 * @method is_tag($tag = '')
 * @method is_tax($taxonomy = '', $term = '')
 * @method is_comments_popup()
 * @method is_date()
 * @method is_day()
 * @method is_feed($feeds = '')
 * @method is_comment_feed()
 * @method is_front_page()
 * @method is_home()
 * @method is_month()
 * @method is_page($page = '')
 * @method is_paged()
 * @method is_preview()
 * @method is_robots()
 * @method is_search()
 * @method is_single($post = '')
 * @method is_singular($post_types = '')
 * @method is_time()
 * @method is_trackback()
 * @method is_year()
 * @method is_404()
 * @method is_embed()
 * @method is_main_query()
 * @method setup_postdata($post)
 * @method reset_postdata()
 * @method lazyload_term_meta($check, $term_id)
 * @method lazyload_comment_meta($check, $comment_id)
 */
class WpQuery
{
    use DecoratorTrait;

    protected $wpQuery;

    /**
     * Constructor
     * @param mixed $query
     * @throws \Error
     * @throws \Exception
     */
    public function __construct($query = null)
    {
        /** @var WpProvider $wpProvider */
        $wpProvider = Core::get(WpProvider::class);

        if (is_null($query)) {
            //Get global WpQuery
            $this->wpQuery = $wpProvider->getWpQuery();

        } else {
            //Make new one
            $this->wpQuery = $wpProvider->newWpQuery($query);
        }
    }

    /**
     * @return mixed
     */
    protected function getDecoratedObject()
    {
        return $this->wpQuery;
    }
}
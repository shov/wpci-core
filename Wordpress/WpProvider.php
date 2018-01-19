<?php declare(strict_types=1);

namespace Wpci\Core\Wordpress;

/**
 * The huge raw facade for WP
 * Provide all wordpress functionality, entities, instances.
 */
class WpProvider
{
    /**
     * WpProvider constructor.
     * @throws \ErrorException
     */
    public function __construct()
    {
        if (!defined('ABSPATH')) {
            throw new \ErrorException("There is no the wordpress!");
        }
    }

    /**
     * WORDPRESS OBJECTS -----------------------------------------------------------------------------------------------
     */

    /**
     * Get global WP instance
     * @return \WP
     * @throws \ErrorException
     */
    public function getWp()
    {
        return $this->getFromGlobal('wp');
    }

    /**
     * Get global WP_Query instance
     * @return \WP_Query
     * @throws \ErrorException
     */
    public function getWpQuery()
    {
        return $this->getFromGlobal('wp_query');
    }

    /**
     * Return new WP_Query instance
     * @param string $query
     * @return \WP_Query
     */
    public function newWpQuery($query = '')
    {
        $wpQueryClass = '\WP_Query';
        return new $wpQueryClass($query);
    }

    /**
     * Return global WP_Post instance
     * @return \WP_Post
     * @throws \ErrorException
     */
    public function getWpPost()
    {
        return $this->getFromGlobal('post');
    }

    /**
     * Return global wpdb instance
     * @return \wpdb
     * @throws \ErrorException
     */
    public function getWpDb()
    {
        return $this->getFromGlobal('wpdb');
    }

    /**
     * Return new wpdb instance
     * @param $dbuser
     * @param $dbpassword
     * @param $dbname
     * @param $dbhost
     * @return \wpdb
     */
    public function newWpDb($dbuser, $dbpassword, $dbname, $dbhost)
    {
        $wpDbClass = '\wpdb';
        return new $wpDbClass($dbuser, $dbpassword, $dbname, $dbhost);
    }

    /**
     * HOOKS -----------------------------------------------------------------------------------------------------------
     */

    /**
     * Add action hook
     * @param string $tag
     * @param callable $callback
     * @param int|null $priority
     * @return mixed
     * @throws \ErrorException
     */
    public function addAction(string $tag, callable $callback, ?int $priority = null)
    {
        $args = [$tag, $callback];
        if (!is_null($priority)) $args[] = $priority;

        return $this->callGlobalFunction('add_action', $args);
    }

    /**
     * Add filter hook
     * @param string $tag
     * @param callable $callback
     * @param int|null $priority
     * @return mixed
     * @throws \ErrorException
     */
    public function addFilter(string $tag, callable $callback, ?int $priority = null)
    {
        $args = [$tag, $callback];
        if (!is_null($priority)) $args[] = $priority;

        return $this->callGlobalFunction('add_filter', $args);
    }


    /**
     * Call the hook
     * @param string $tag
     * @param array $params
     * @return mixed
     * @throws \ErrorException
     */
    public function doAction(string $tag, ...$params)
    {
        $args = [$tag];
        if (!empty($params)) $args[] = $params;

        return $this->callGlobalFunction('do_action', $args);
    }

    /**
     * Apply the filter hook
     * @param string $tag
     * @param $value
     * @return mixed
     * @throws \ErrorException
     */
    public function applyFilters(string $tag, $value)
    {
        return $this->callGlobalFunction('apply_filters', [$tag, $value]);
    }

    /**
     * HELPERS ---------------------------------------------------------------------------------------------------------
     */

    /**
     * Get blog info data
     * @param string $key
     * @return mixed
     * @throws \ErrorException
     */
    public function getBlogInfo(string $key)
    {
        return $this->callGlobalFunction('get_bloginfo');
    }

    /**
     * Get the option
     * @param string $key
     * @param bool $default
     * @return mixed
     * @throws \ErrorException
     */
    public function getOption(string $key, $default = false)
    {
        return $this->callGlobalFunction('get_option', [$key, $default]);
    }

    /**
     * Get all registered menu locations
     * @return mixed
     * @throws \ErrorException
     */
    public function getNavMenuLocations()
    {
        return $this->callGlobalFunction('get_nav_menu_locations');
    }

    /**
     * Get all items from the menu
     * @param $menu
     * @param array $args
     * @return mixed
     * @throws \ErrorException
     */
    public function wpGetNavMenuItems($menu, $args = [])
    {
        return $this->callGlobalFunction('wp_get_nav_menu_items', [$menu, $args]);
    }

    /**
     * Retrieves a modified URL query string
     * @param array $args
     * @return mixed
     * @throws \ErrorException
     */
    public function addQueryArg(...$args)
    {
        return $this->callGlobalFunction('add_query_arg', $args);
    }

    /**
     * Retrieves the URL for the current site where the front end is accessible
     * @param string $path
     * @param null $scheme
     * @return mixed
     * @throws \ErrorException
     */
    public function homeUrl($path = '', $scheme = null)
    {
        return $this->callGlobalFunction('home_url', [$path, $scheme]);
    }

    /**
     * Retrieve theme directory URI
     * @return mixed
     * @throws \ErrorException
     */
    public function getTemplateDirectoryUri()
    {
        return $this->callGlobalFunction('get_template_directory_uri');
    }

    /**
     * Set HTTP status header
     * @param $code
     * @param string $description
     * @return mixed
     * @throws \ErrorException
     */
    public function statusHeader($code, $description = '')
    {
        return $this->callGlobalFunction('status_header', [$code, $description]);
    }

    /**
     * LOOP ------------------------------------------------------------------------------------------------------------
     */

    /**
     * Print wp head
     * @return mixed
     * @throws \ErrorException
     */
    public function wpHead()
    {
        return $this->callGlobalFunction('wp_head');
    }

    /**
     * Print wp footer
     * @return mixed
     * @throws \ErrorException
     */
    public function wpFooter()
    {
        return $this->callGlobalFunction('wp_footer');
    }

    /**
     * Retrieve post title
     * @param int $post
     * @return mixed
     * @throws \ErrorException
     */
    public function getTheTitle($post = 0)
    {
        return $this->callGlobalFunction('get_the_title', [$post]);
    }

    /**
     * Display the post content
     * @param null $moreLinkText
     * @param bool $stripTeaser
     * @return mixed
     * @throws \ErrorException
     */
    public function theContent($moreLinkText = null, $stripTeaser = false)
    {
        return $this->callGlobalFunction('the_content', [$moreLinkText, $stripTeaser]);
    }

    /**
     * Destroys the previous query and sets up a new query
     * @return mixed
     * @throws \ErrorException
     */
    public function wpResetQuery()
    {
        return $this->callGlobalFunction('wp_reset_query');
    }

    /**
     * Restores the $post global to the current post in the main query
     * @return mixed
     * @throws \ErrorException
     */
    public function wpResetPostData()
    {
        return $this->callGlobalFunction('wp_reset_postdata');
    }

    /**
     * Get all the custom field values for a specific postId
     * @param bool $postId
     * @param bool $formatValue
     * @return mixed
     * @throws \ErrorException
     */
    public function getFields($postId = false, $formatValue = true)
    {
        return $this->callGlobalFunction('get_fields', [$postId, $formatValue]);
    }

    /**
     * Get custom field value for a specific field name/key + postId
     * @param $key
     * @param bool $postId
     * @param bool $formatValue
     * @return mixed
     * @throws \ErrorException
     */
    public function getField($key, $postId = false, $formatValue = true)
    {
        return $this->callGlobalFunction('get_field', [$key, $postId, $formatValue]);
    }

    /**
     * Assets ----------------------------------------------------------------------------------------------------------
     */

    /**
     * Enqueue a CSS stylesheet
     * @param $handle
     * @param string $src
     * @param array $deps
     * @param bool $ver
     * @param string $media
     * @return mixed
     * @throws \ErrorException
     */
    public function wpEnqueueStyle($handle, $src = '', $deps = [], $ver = false, $media = 'all')
    {
        return $this->callGlobalFunction('wp_enqueue_style', [$handle, $src, $deps, $ver, $media]);
    }

    /**
     * Register a CSS stylesheet
     * @param $handle
     * @param $src
     * @param array $deps
     * @param bool $ver
     * @param string $media
     * @return mixed
     * @throws \ErrorException
     */
    public function wpRegisterStyle($handle, $src, $deps = [], $ver = false, $media = 'all')
    {
        return $this->callGlobalFunction('wp_register_style', [$handle, $src, $deps, $ver, $media]);
    }

    /**
     * Enqueue a script
     * @param $handle
     * @param string $src
     * @param array $deps
     * @param bool $ver
     * @param bool $inFooter
     * @return mixed
     * @throws \ErrorException
     */
    public function wpEnqueueScript($handle, $src = '', $deps = [], $ver = false, $inFooter = false)
    {
        return $this->callGlobalFunction('wp_enqueue_script', [$handle, $src, $deps, $ver, $inFooter]);
    }

    /**
     * Register a new script
     * @param $handle
     * @param $src
     * @param array $deps
     * @param bool $ver
     * @param bool $inFooter
     * @return mixed
     * @throws \ErrorException
     */
    public function wpRegisterScript($handle, $src, $deps = [], $ver = false, $inFooter = false)
    {
        return $this->callGlobalFunction('wp_register_script', [$handle, $src, $deps, $ver, $inFooter]);
    }

    /**
     * Localize a script
     * @param $handle
     * @param $objectName
     * @param $l10n
     * @return mixed
     * @throws \ErrorException
     */
    public function wpLocalizeScript($handle, $objectName, $l10n)
    {
        return $this->callGlobalFunction('wp_localize_script', [$handle, $objectName, $l10n]);
    }

    /**
     * REST ------------------------------------------------------------------------------------------------------------
     */

    /**
     * Registers a REST API route
     * @param $namespace
     * @param $route
     * @param array $args
     * @param bool $override
     * @return mixed
     * @throws \ErrorException
     */
    public function registerRestRoute($namespace, $route, $args = [], $override = false)
    {
        return $this->callGlobalFunction('register_rest_route', [$namespace, $route, $args, $override]);
    }

    /**
     * -----------------------
     * Internal helpers ------------------------------------------------------------------------------------------------
     * -----------------------
     */

    /**
     * Get global namespace variable
     * @param string $name
     * @return mixed
     * @throws \ErrorException
     */
    protected function getFromGlobal(string $name)
    {
        if (isset($GLOBALS[$name])) {
            return $GLOBALS[$name];
        }

        throw new \ErrorException(
            sprintf("Undefined global variable (%s) called!", $name));
    }

    /**
     * Set global namespace variable
     * @param $name
     * @param $value
     */
    protected function setGlobal(string $name, $value)
    {
        $GLOBALS[$name] = $value;
    }

    /**
     * Call global namespace function
     * @param string $name
     * @param array|null $arguments
     * @return mixed
     * @throws \ErrorException
     */
    protected function callGlobalFunction(string $name, ?array $arguments = null)
    {
        if (function_exists($name)) {
            return call_user_func_array($name, $arguments ?? []);
        }

        throw new \ErrorException(
            sprintf("Undefined global function (%s) called!", $name));
    }
}
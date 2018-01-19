<?php declare(strict_types=1);

namespace Wpci\Core\DataSource;

use stdClass;
use WP_Post;
use WP_Query;
use Wpci\Core\Facades\Core;
use Wpci\Core\Facades\Path;
use Wpci\Core\Helpers\DataManipulatorTrait;
use Wpci\Core\Helpers\DecoratorTrait;
use Wpci\Core\Wordpress\WpPost;
use Wpci\Core\Wordpress\WpProvider;
use Wpci\Core\Wordpress\WpQuery;

/**
 * General data source uses as wordpress native query wrapper,
 * provides smart and flexible way to collect necessary data in pretty useful shape
 */
class WpciQuery
{
    use DecoratorTrait;
    use DataManipulatorTrait;

    const DEFAULT_CHANNEL = 'default';

    /**
     * Receive default data channel name
     * @see DataManipulatorTrait::getDefaultChannel()
     * @return string
     */
    protected static function getDefaultChannel(): string
    {
        return static::DEFAULT_CHANNEL;
    }

    /** @var WpProvider */
    protected $wpProvider;

    /** @var WpQuery */
    protected $decoratedWpQuery = null;

    /**
     * WpciQuery constructor.
     * @param null $query
     * @param null|string $channel
     * @throws \Exception
     * @throws \Error
     */
    public function __construct($query = null, ?string $channel = null)
    {
        $this->wpProvider = Core::get(WpProvider::class);

        $this->addVariables(static::getBaseDataByChannel($channel));

        switch(true) {
            case ($query instanceof WpQuery):
                $this->decoratedWpQuery = $query;
                break;

            case (!empty($query) && (is_string($query) || is_array($query))):
                $this->decoratedWpQuery = new WpQuery($query);
                break;

            default:
                $this->decoratedWpQuery = Core::get(WpQuery::class);
                break;
        }
    }

    /**
     * Add wordpress environment to the data
     * @return WpciQuery
     * @throws \ErrorException
     */
    public function addWpEnv()
    {
        $wpp = $this->wpProvider;
        $data = [
            'site-url' => $wpp->getBlogInfo('url'),
            'site-wpurl' => $wpp->getBlogInfo('wpurl'),
            'site-description' => $wpp->getBlogInfo('description'),
            'site-rss_url' => $wpp->getBlogInfo('rss_url'),
            'site-rss2_url' => $wpp->getBlogInfo('rss2_url'),
            'site-atom_url' => $wpp->getBlogInfo('atom_url'),
            'site-comments_atom_url' => $wpp->getBlogInfo('comments_atom_url'),
            'site-comments_rss2_url' => $wpp->getBlogInfo('comments_rss2_url'),
            'site-pingback_url' => $wpp->getBlogInfo('pingback_url'),
            'site-stylesheet_url' => $wpp->getBlogInfo('stylesheet_url'),
            'site-stylesheet_directory' => $wpp->getBlogInfo('stylesheet_directory'),
            'site-template_directory' => $wpp->getBlogInfo('template_directory'),
            'site-admin_email' => $wpp->getBlogInfo('admin_email'),
            'site-charset' => $wpp->getBlogInfo('charset'),
            'site-html_type' => $wpp->getBlogInfo('html_type'),
            'site-version' => $wpp->getBlogInfo('version'),
            'site-language' => $wpp->getBlogInfo('language'),
            'site-name' => $wpp->getBlogInfo('name'),
        ];

        $this->mergeToTheData($data);
        return $this;
    }

    /**
     * Add to result current post data
     * @param callable|null $anotherElse
     * @param bool $withoutWp
     * @return WpciQuery
     * @throws \Exception
     * @throws \Error
     */
    public function addPostData(?callable $anotherElse = null, bool $withoutWp = false)
    {
        /** @var WpPost $post global post */
        $post = Core::get(WpPost::class);

        $queryObject = $this->wpQuery();

        $data = [];
        if ($queryObject->have_posts()) {
            $queryObject->the_post();
            $data['title'] = $this->wpProvider->getTheTitle();

            ob_start();
            $this->wpProvider->theContent();
            $data['content'] = ob_get_clean();

            $postData['excerpt'] = $post->post_excerpt;

            $data = array_merge($this->getAcfFromPage($post->ID), $data);
            if (!is_null($anotherElse)) {
                $anotherElse($data, $post);
            }
        }

        if (!$withoutWp) {
            ob_start();
            $this->wpProvider->wpHead();
            $data['wp-head'] = ob_get_clean();

            ob_start();
            $this->wpProvider->wpFooter();
            $data['wp-footer'] = ob_get_clean();
        }

        $queryObject->rewind_posts();
        $this->wpProvider->wpResetPostData();

        $this->mergeToTheData($data);
        return $this;
    }

    /**
     * Add to result post data in wp loop
     * @param callable|null $anotherElse
     * @param bool $withoutWp
     * @return WpciQuery
     * @throws \Exception
     * @throws \Error
     */
    public function addPostLoopData(?callable $anotherElse = null, bool $withoutWp = false)
    {
        /** @var WpPost $post global post */
        $post = Core::get(WpPost::class);

        $queryObject = $this->wpQuery();

        $data = [];
        while ($queryObject->have_posts()) {
            $queryObject->the_post();
            $postData = [];
            $postData['title'] = $this->wpProvider->getTheTitle();

            ob_start();
            $this->wpProvider->theContent();
            $postData['content'] = ob_get_clean();

            $postData['excerpt'] = $post->post_excerpt;

            $postData = array_merge($this->getAcfFromPage($post->ID), $postData);

            $data['posts'][] = $postData;

            if (!is_null($anotherElse)) {
                $anotherElse($data['posts'][count($data['posts']) - 1], $post);
            }
        }

        if (is_object($queryObject->queried_object)) {
            $data['title'] = $queryObject->queried_object->name;
        }

        if (!$withoutWp) {
            ob_start();
            $this->wpProvider->wpHead();
            $data['wp-head'] = ob_get_clean();

            ob_start();
            $this->wpProvider->wpFooter();
            $data['wp-footer'] = ob_get_clean();
        }

        $queryObject->rewind_posts();
        $this->wpProvider->wpResetPostData();

        $this->mergeToTheData($data);
        return $this;
    }

    /**
     * Add menus to template
     * @return $this
     * @throws \ErrorException
     */
    public function addMenu()
    {
        $menuLocations = $this->wpProvider
            ->getNavMenuLocations();

        $data = [];

        foreach ($menuLocations as $menuLocation => $menuId) {
            $menu = $this->wpProvider
                ->wpGetNavMenuItems($menuId);

            foreach ($menu as $menuItem) {
                $item = new stdClass();

                /**
                 * @var WpPost $menuItem
                 */
                $item->item = $menuItem->title;
                $item->link = $menuItem->url;
                $item->active = (Path::getCurrentUrl() === $menuItem->url) ? true : false;

                $data[$menuLocation][] = $item;
            }
        }

        $this->mergeToTheData($data);
        return $this;
    }

    /**
     * Try to add page's ACF date to result
     * @param int $pageId
     * @return $this
     */
    public function addAcfFromPage(int $pageId)
    {
        $this->mergeToTheData($this->getAcfFromPage($pageId));
        return $this;
    }

    /**
     * Try to add homepage ACF data to result
     * @return $this
     * @throws \ErrorException
     */
    public function addHomePageAcf()
    {
        $homePageId = (int)$this->wpProvider->getOption('page_on_front');
        $this->addAcfFromPage($homePageId);
        return $this;
    }

    /**
     * Right way to get current and correct query object
     * @return WpQuery
     * @throws \Exception
     * @throws \Error
     */
    protected function wpQuery(): WpQuery
    {
        $hashDecoratedObject = spl_object_hash($this->decoratedWpQuery);
        $globalObject = spl_object_hash(Core::get(WpQuery::class));

        if ($hashDecoratedObject === $globalObject) {
            $this->wpProvider->wpResetQuery();
        }

        return $this->decoratedWpQuery;
    }

    /**
     * Decorate WP_Query object
     * @see DecoratorTrait::getDecoratedObject()
     * @return WpQuery
     */
    protected function getDecoratedObject(): WpQuery
    {
        return $this->decoratedWpQuery;
    }

    /**
     * Try to receive the ACF data by page id, will return empty array if fail
     * @param int $pageId
     * @return array
     */
    protected function getAcfFromPage(int $pageId): array
    {
        $data = [];
        try {
            $data = $this->wpProvider->getFields($pageId);
        } catch (\Throwable $e) {
            ;
        }

        if (!is_array($data) || empty($data)) return [];

        return $data;
    }
}
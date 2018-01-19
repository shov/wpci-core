<?php declare(strict_types=1);

namespace Wpci\Core\Wordpress;

use Wpci\Core\Helpers\DecoratorTrait;

/**
 * The \WP class proxy
 * @version wp 4.9.1
 *
 * @method add_query_var($qv)
 * @method remove_query_var($name);
 * @method set_query_var($key, $value);
 * @method parse_request($extra_query_vars = '');
 * @method send_headers();
 * @method build_query_string();
 * @method register_globals();
 * @method init();
 * @method query_posts();
 * @method handle_404();
 * @method main($query_args = '');
 *
 * @property array $public_query_vars;
 * @property array $private_query_vars;
 * @property array $extra_query_vars;
 * @property $query_vars;
 * @property $query_string;
 * @property $request;
 * @property $matched_rule;
 * @property $matched_query;
 * @property bool $did_permalink;
 */
class WP
{
    use DecoratorTrait;

    protected $wp;

    /**
     * DI
     * @param WpProvider $wpProvider
     * @throws \ErrorException
     */
    public function __construct(WpProvider $wpProvider)
    {
        $this->wp = $wpProvider->getWp();
    }

    /**
     * @return mixed
     */
    protected function getDecoratedObject()
    {
        return $this->wp;
    }
}
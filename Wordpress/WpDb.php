<?php declare(strict_types=1);

namespace Wpci\Core\Wordpress;

use Wpci\Core\Facades\Core;
use Wpci\Core\Helpers\DecoratorTrait;

/**
 * The \WP_Query class proxy
 * @version wp 4.9.1
 *
 * @property $last_error = '';
 * @property $num_queries = 0;
 * @property $num_rows = 0;
 * @property $insert_id = 0;
 * @property $prefix = '';
 * @property $base_prefix;
 * @property $blogid = 0;
 * @property $siteid = 0;
 * @property $comments;
 * @property $commentmeta;
 * @property $links;
 * @property $options;
 * @property $postmeta;
 * @property $posts;
 * @property $terms;
 * @property $term_relationships;
 * @property $term_taxonomy;
 * @property $termmeta;
 * @property $usermeta;
 * @property $users;
 * @property $blogs;
 * @property $blog_versions;
 * @property $registration_log;
 * @property $signups;
 * @property $site;
 * @property $sitecategories;
 * @property $sitemeta;
 * @property $field_types = array();
 * @property $charset;
 * @property $collate;
 * @property $func_call;
 * @property $is_mysql = null;
 *
 * @method __destruct()
 * @method __get($name)
 * @method __set($name, $value)
 * @method __isset($name)
 * @method __unset($name)
 * @method init_charset()
 * @method determine_charset($charset, $collate)
 * @method set_charset($dbh, $charset = null, $collate = null)
 * @method set_sql_mode($modes = array())
 * @method set_prefix($prefix, $set_table_names = true)
 * @method set_blog_id($blog_id, $network_id = 0)
 * @method get_blog_prefix($blog_id = null)
 * @method tables($scope = 'all', $prefix = true, $blog_id = 0)
 * @method select($db, $dbh = null)
 * @method _escape($data)
 * @method escape($data)
 * @method escape_by_ref(&$string)
 * @method prepare($query, $args)
 * @method esc_like($text)
 * @method print_error($str = '')
 * @method show_errors($show = true)
 * @method hide_errors()
 * @method suppress_errors($suppress = true)
 * @method flush()
 * @method db_connect($allow_bail = true)
 * @method parse_db_host($host)
 * @method check_connection($allow_bail = true)
 * @method query($query)
 * @method placeholder_escape()
 * @method add_placeholder_escape($query)
 * @method remove_placeholder_escape($query)
 * @method insert($table, $data, $format = null)
 * @method replace($table, $data, $format = null)
 * @method update($table, $data, $where, $format = null, $where_format = null)
 * @method delete($table, $where, $where_format = null)
 * @method get_var($query = null, $x = 0, $y = 0)
 * @method get_row($query = null, $output = OBJECT, $y = 0)
 * @method get_col($query = null, $x = 0)
 * @method get_results($query = null, $output = OBJECT)
 * @method get_col_charset($table, $column)
 * @method get_col_length($table, $column)
 * @method strip_invalid_text_for_column($table, $column, $value)
 * @method get_col_info($info_type = 'name', $col_offset = -1)
 * @method timer_start()
 * @method timer_stop()
 * @method bail($message, $error_code = '500')
 * @method close()
 * @method check_database_version()
 * @method supports_collation()
 * @method get_charset_collate()
 * @method has_cap($db_cap)
 * @method get_caller()
 * @method db_version()
 */
class WpDb
{

    use DecoratorTrait;

    protected $wpDb;

    /**
     * Constructor
     * @param null|string $dbuser
     * @param null|string $dbpassword
     * @param null|string $dbname
     * @param null|string $dbhost
     * @throws \Error
     * @throws \ErrorException
     * @throws \Exception
     */
    public function __construct(?string $dbuser = null,
                                ?string $dbpassword = null,
                                ?string $dbname = null,
                                ?string $dbhost = null)
    {
        /** @var WpProvider $wpProvider */
        $wpProvider = Core::get(WpProvider::class);

        $squash = $dbuser ?? $dbpassword ?? $dbname ?? $dbhost ?? null;

        if (is_null($squash)) {
            //Get global WpDb
            $this->wpDb = $wpProvider->getWpDb();

        } else {
            //Make new one
            $this->wpDb = $wpProvider->newWpDb($dbuser, $dbpassword, $dbname, $dbhost);
        }
    }

    /**
     * @return mixed
     */
    protected function getDecoratedObject()
    {
        return $this->wpDb;
    }
}
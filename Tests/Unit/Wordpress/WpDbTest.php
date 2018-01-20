<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit;

use Wpci\Core\Tests\TestCase;
use Wpci\Core\Wordpress\WpDb;

/**
 * @see WpDb
 */
class WpDbTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function getGlobal()
    {
        //Arrange
        $this->mockWpdb();

        $expectedDbVersion = implode('#', [
                'dbuser' => 'super',
                'dbpassword' => 'duper',
                'dbname' => 'awesome',
                'dbhost' => 'powerful'
            ]) . '#TestVersion##';

        //Act
        /** @var WpDb $wpdb */
        $wpdb = $this->coreGet(WpDb::class);

        //Assert
        $this->assertSame($expectedDbVersion, $wpdb->db_version());

    }

    /**
     * @test
     */
    public function getNewInstance()
    {
        //Arrange
        $this->mockWpdb();

        $expectedDbVersion = implode('#', [
                'dbuser' => 'lala',
                'dbpassword' => 'lalala',
                'dbname' => 'snooop',
                'dbhost' => 'doog'
            ]) . '#TestVersion##';

        //Act
        /** @var WpDb $wpdb */
        $wpdb = new WpDb('lala', 'lalala', 'snooop', 'doog');

        //Assert
        $this->assertSame($expectedDbVersion, $wpdb->db_version());
    }

    protected function mockWpdb()
    {
        $globalClassName = '\\wpdb';
        if (!class_exists($globalClassName)) {

            /** @lang php */
            $php = <<<'CODE'
                class wpdb {
                    public $initParams = [];
                    
                    public function __construct($dbuser, $dbpassword, $dbname, $dbhost) {
                        $this->initParams = compact('dbuser', 'dbpassword', 'dbname', 'dbhost');
                    }
                    
                    public function db_version()
                    {
                        return implode('#', $this->initParams) . '#TestVersion##';
                    }
                }
CODE;

            eval($php);

            global $wpdb;
            $wpdb = new $globalClassName('super', 'duper', 'awesome', 'powerful');
        }

    }
}
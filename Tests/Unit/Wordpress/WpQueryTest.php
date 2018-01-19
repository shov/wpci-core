<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit;

use Wpci\Core\Tests\TestCase;
use Wpci\Core\Wordpress\WpQuery;

/**
 * @see WpQuery
 */
class WpQueryTest extends TestCase
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
        $this->mockWpQuery();

        $expectedQuery = '2989';

        //Act
        /** @var WpQuery $wpQuery */
        $wpQuery = $this->coreGet(WpQuery::class);

        //Assert
        $this->assertSame($expectedQuery, $wpQuery->query);

    }

    /**
     * @test
     */
    public function getNewInstance()
    {
        //Arrange
        $this->mockWpQuery();

        $expectedQuery = 'newQuery=1&cat=2';

        //Act
        /** @var WpQuery $wpQuery */
        $wpQuery = new WpQuery($expectedQuery);

        //Assert
        $this->assertSame($expectedQuery, $wpQuery->query);
    }

    protected function mockWpQuery()
    {
        $globalClassName = '\\WP_Query';
        if (!class_exists($globalClassName)) {

            /** @lang php */
            $php = <<<'CODE'
                class WP_Query {
                    public $query;
                    
                    public function __construct($query = '') {
                        $this->query = $query;
                    }
                }
CODE;

            eval($php);

            global $wp_query;
            $wp_query = new $globalClassName('2989');
        }

    }
}
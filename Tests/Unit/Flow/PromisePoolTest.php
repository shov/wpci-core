<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Flow;

use Wpci\Core\Flow\PromisePool;
use Wpci\Core\Tests\TestCase;

class PromisePoolTest extends TestCase
{
    /**
     * @test
     */
    public function callAllFromNothing()
    {
        //Arrange
        $pool = new PromisePool();
        $ok = true;

        //Act
        $pool->callAllPromises();

        //Assert
        $this->assertTrue($ok);
    }

    /**
     * @test
     */
    public function callNotExistingPromiseByName()
    {
        //Arrange
        $pool = new PromisePool();

        //Assert
        $this->expectException(\InvalidArgumentException::class);

        //Act
        $pool->callPromise('hello!');
    }

    /**
     * @test
     */
    public function callAnonPromiseByName()
    {
        //Arrange
        $pool = new PromisePool();
        $counter = 0;

        $pool->addAnonymousPromise(function() use (&$counter) {
            $counter++;
        });

        //Act
        $pool->callPromise('0');

        //Assert
        $this->assertSame(1, $counter);
    }

    /**
     * @test
     */
    public function callNamedPromiseByName()
    {
        //Arrange
        $pool = new PromisePool();
        $counter = 0;
        $name = 'ly87ghghgfpihg';
        $pool->addPromise($name, function() use (&$counter) {
            $counter++;
        });

        //Act
        $pool->callPromise($name);

        //Assert
        $this->assertSame(1, $counter);
    }

    /*
     * @test
     */
    public function callAllWithPriority()
    {
        //Arrange
        $pool = new PromisePool();
        $counter= '';

        $pool->addAnonymousPromise(function () use (&$counter) {
            $counter .= 'A';
        }); //Here default priority

        $pool->addAnonymousPromise(function () use (&$counter) {
            $counter .= 'L';
        }, 9000);

        $pool->addPromise('Obscene low', function () use (&$counter) {
            $counter .= 'O';
        }, 100500);

        $pool->addPromise('High priority', function () use (&$counter) {
           $counter .= 'H';
        }, 1);

        //Act
        $pool->callAllPromises();

        //Assert
        $this->assertSame('HALO', $counter);
    }
}
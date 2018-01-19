<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Flow;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wpci\Core\Flow\ContainerManager;
use Wpci\Core\Tests\TestCase;

class ContainerManagerTest extends TestCase
{
    public function tearDown()
    {
        //stub
    }

    /**
     * @test
     */
    public function testInit()
    {
        /** @var ContainerManager $cm */
        $cm = $this->core->getContainerManager();

        $counter = 0;
        $cm->initInstructions(function (ContainerBuilder $containerBuilder) use (&$counter){
            $this->assertTrue(true);
            $counter++;
        });

        $cm->createContainer();

        $this->assertSame(1, $counter);
    }
}
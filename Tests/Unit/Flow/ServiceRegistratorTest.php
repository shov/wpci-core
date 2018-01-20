<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Flow;

use Illuminate\Container\Container;
use Symfony\Component\Filesystem\Filesystem;
use Wpci\Core\Flow\ServiceRegistrator;
use Wpci\Core\Tests\TestCase;

class ServiceRegistratorTest extends TestCase
{
    /** @var Filesystem */
    protected $fs;

    /** @var string */
    protected $testDir;

    public function setUp()
    {
        $this->fs = new Filesystem();
        $this->testDir = __DIR__ . '/Tmp';

        $this->fs->mkdir($this->testDir);
        $this->fs->mkdir($this->testDir . '/Part');

        parent::setUp();
    }

    /**
     * @test
     */
    public function testWalking()
    {
        //Arrange
        $this->prepare();
        $baseNs = "Wpci\\Core\\Tests\\Unit\\Flow\\Tmp\\";

        /** @var Container $container */
        $container = $this->core
            ->getContainerManager()
            ->getContainer();
        $sr = new ServiceRegistrator($this->testDir, $baseNs, $container);

        $sr->exclude($baseNs . 'Part\\Betta');

        //Act
        $sr->walkDirForServices('Part');

        //Assert
        $this->assertTrue($container->has($baseNs . 'Part\\Alpha'));

        $this->assertFalse($container->has($baseNs . 'Part\\Betta'));
        $this->assertFalse($container->has($baseNs . 'Part\\Gamma'));
        $this->assertFalse($container->has($baseNs . 'Part\\Sub\\Gamma'));
        $this->assertFalse($container->has($baseNs . 'Part\\Delta'));
        $this->assertFalse($container->has($baseNs . 'Part\\NoDelta'));

        $this->assertSame('Alpha', $container->get($baseNs . 'Part\\Alpha')->foo());
    }

    protected function prepare()
    {
        $serviceAlpha = <<<'CODE'
        <?php
        
        namespace Wpci\Core\Tests\Unit\Flow\Tmp\Part;
        
        class Alpha {
            public function foo()
            {
                return 'Alpha';
            }
        }
CODE;
        $serviceAlpha = ltrim($serviceAlpha);
        $serviceBetta = str_replace('Alpha', 'Betta', $serviceAlpha);
        $serviceGamma = str_replace('Alpha', 'Gamma', $serviceAlpha);
        $serviceDelta = str_replace('Alpha', 'Delta', $serviceAlpha);

        $this->fs->dumpFile($this->testDir . '/Part/Alpha.php', $serviceAlpha);
        $this->fs->dumpFile($this->testDir . '/Part/Betta.php', $serviceBetta);
        $this->fs->dumpFile($this->testDir . '/Part/Sub/Gamma.php', $serviceGamma);
        $this->fs->dumpFile($this->testDir . '/Part/NoDelta.php', $serviceDelta);
    }

    public function tearDown()
    {
       $this->fs->remove($this->testDir);
    }
}
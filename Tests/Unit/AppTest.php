<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit;

use Illuminate\Container\Container;
use Monolog\Logger;
use Wpci\Core\Contracts\AbstractApp;
use Wpci\Core\Facades\Core;
use Wpci\Core\Flow\PromiseManager;
use Wpci\Core\Tests\TestCase;
use Wpci\Core\Wordpress\WpProvider;

class AppTest extends TestCase
{
    /**
     * @test
     */
    public function emulateRun()
    {
        $phpMockWpAddAction = <<<'CODE'
            function add_action()
            {
                ;
            }
CODE;
        if(!function_exists('\add_action')) {
            eval($phpMockWpAddAction);
        }


        $pmMock = new class(new WpProvider()) extends PromiseManager
        {
            public $promise = [];

            public function addPromise(string $name, callable $promise, ?int $priority = null)
            {
                $this->promise[] = $name;
            }
        };

        //Arrange
        (new class ('', $pmMock) extends AbstractApp
        {
            protected $pmMock;

            public function __construct(string $appRoot, $pmMock)
            {
                $this->pmMock = $pmMock;
                parent::__construct($appRoot);
            }

            protected function setPath()
            {
                /** @var Container $cb */
                $cb = $this->core->getContainerManager()->getContainer();
                $cb->instance(PromiseManager::class, $this->pmMock);
            }

            protected function setUpEnvironment()
            {
                //do nothing
            }

            protected function setUpServices()
            {
                //do nothing
            }

            public function run()
            {
                /** @var PromiseManager $pm */
                $pm = Core::get(PromiseManager::class);
                $pm->addPromise('GoingTest', 'assert');
            }
        })->beforeRun(function (\Wpci\Core\Core $core) {

            $core->setLogger(new class('test') extends Logger
            {
                public function error($message, array $context = array())
                {
                    dump([$message]);
                }
            });

        })->handle($this->core);

        $this->assertSame(['shutdown', 'GoingTest'], $pmMock->promise);
    }
}
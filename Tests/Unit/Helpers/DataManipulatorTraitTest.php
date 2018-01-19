<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Helpers;

use Wpci\Core\Helpers\DataManipulatorTrait;
use Wpci\Core\Tests\TestCase;

class DataManipulatorTraitTest extends TestCase
{
    /**
     * @test
     */
    public function getEmpty()
    {
        //Arrange
        $manipulator = $this->mocking();

        //Act
        $data = $manipulator->fetch();

        //Assert
        $this->assertTrue(is_array($data) && empty($data));
    }

    /**
     * @test
     */
    public function addAndGetVariables()
    {
        //Arrange
        $manipulator = $this->mocking();
        $manipulator->addVariables(['key1' => 'foo', 'secKey' => 'bar']);
        $manipulator->addVariables(['secKey' => 'baz']);
        $manipulator->addVariables(['key1' => null]);

        //Act
        $data = $manipulator->fetch();

        //Assert
        $this->assertCount(2, $data);
        $this->assertJsonStringEqualsJsonString(json_encode(['secKey' => 'baz', 'key1' => null]), json_encode($data));
    }

    /**
     * @test
     */
    public function baseVariable()
    {
        //Arrange
        $manipulator = $this->mocking();

        $defaultSrc = ['normal1' => 1, 'normal2' => 'n2'];
        $manipulator::addBaseVariables($defaultSrc);

        $specSrc = ['spec1' => 1, 'spec2' => 's2'];
        $manipulator::addBaseVariables($specSrc, 'spec');

        //Act
        $defaultData = $manipulator::getBaseDataByChannel();
        $specData = $manipulator::getBaseDataByChannel('spec');

        //Assert
        $this->assertJsonStringEqualsJsonString(json_encode($defaultSrc), json_encode($defaultData));
        $this->assertJsonStringEqualsJsonString(json_encode($specSrc), json_encode($specData));
    }

    /**
     * @test
     */
    public function modifyValues()
    {
        //Arrange
        $manipulator = $this->mocking();
        $manipulator->addVariables([
            'box' => 2,
            'text' => 'Lorem',
            'title' => 'the ring',
        ]);

        //Act
        $manipulator->modifyValues([
            'box' => 4,
            'text' => function ($text) {
                return $text . '.';
            },
            'title' => function ($title) {
                return strtoupper($title);
            },
            'test' => function () {
                return 69.0;
            },
            'legion' => '(X)',
        ]);

        $data = $manipulator->fetch();

        //Assert
        $this->assertJsonStringEqualsJsonString(json_encode([
            'box' => 4,
            'text' => 'Lorem.',
            'title' => 'THE RING',
            'test' => 69.0,
            'legion' => '(X)',
        ]), json_encode($data));
    }

    /**
     * @test
     */
    public function modifyValuesSubkey()
    {
        //Arrange
        $manipulator = $this->mocking();
        $manipulator->addVariables([
            'posts' => [
                [
                    'box' => 2,
                    'text' => 'Lorem',
                    'title' => 'the ring',
                ]
            ]
        ]);

        //Act
        $manipulator->modifyValues([
            'box' => 4,
            'text' => function ($text) {
                return $text . '.';
            },
            'title' => function ($title) {
                return strtoupper($title);
            },
            'test' => function () {
                return 69.0;
            },
            'legion' => '(X)',
        ], 'posts');

        $data = $manipulator->fetch();

        //Assert
        $this->assertJsonStringEqualsJsonString(json_encode([
            'posts' => [
                [
                    'box' => 4,
                    'text' => 'Lorem.',
                    'title' => 'THE RING',
                    'test' => 69.0,
                    'legion' => '(X)',
                ]
            ]
        ]), json_encode($data));
    }

    /**
     * @test
     */
    public function modifyValuesOneSubkey()
    {
        //Arrange
        $manipulator = $this->mocking();
        $manipulator->addVariables([
            'single' => [
                'box' => 2,
                'text' => 'Lorem',
                'title' => 'the ring',
            ]
        ]);

        //Assert
        $this->expectException(\InvalidArgumentException::class);

        //Act
        $manipulator->modifyValues([
            'box' => 4,
        ], 'single');
    }

    protected function mocking()
    {
        $manipulator = new class ()
        {
            use DataManipulatorTrait {
                getBaseDataByChannel as protected traitGetBaseDataByChannel;
            }


            public static function getDefaultChannel()
            {
                return 'test_channel';
            }

            public static function getBaseDataByChannel(?string $channel = null): array
            {
                return static::traitGetBaseDataByChannel($channel);
            }
        };

        return $manipulator;
    }
}
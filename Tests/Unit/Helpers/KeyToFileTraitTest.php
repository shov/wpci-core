<?php declare(strict_types=1);

namespace Wpci\Core\Tests\Unit\Helpers;

use Wpci\Core\Facades\ShutdownPromisePool;
use Wpci\Core\Helpers\KeyToFileTrait;
use Wpci\Core\Tests\TestCase;

class KeyToFileTraitTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown();
        @unlink(__DIR__ . '/test.html');
    }

    /**
     * @test
     * @throws \Exception
     */
    public function tempFile()
    {
        //Arrange
        $content = '<html><p>Hello</p></html>';

        $keyToFile = $this->mocking();
        $fileMustBeUnlinked = [];

        $callback = function ($filePath) use (&$content, &$fileMustBeUnlinked) {
            //Assert
            $this->assertSame(substr($filePath, -5), '.html');
            $this->assertEquals($content, file_get_contents($filePath));

            $fileMustBeUnlinked[] = $filePath;
        };

        //Act
        $keyToFile->keyToFileForProcess(__DIR__, $content, $callback, 'html');

        $content .= '2';
        $keyToFile->keyToFileForProcess(__DIR__, $content, $callback, '.html');

        ShutdownPromisePool::callAllPromises();

        array_map(function($exceptUnlinkedFile) {
            $this->assertTrue(!file_exists($exceptUnlinkedFile));
        }, $fileMustBeUnlinked);
    }

    /**
     * @test
     * @dataProvider fileKeyDataProvider
     * @throws \Exception
     */
    public function filePath($key)
    {
        //Arrange
        $content = '<html><p>Hello</p></html>';

        $this->assertTrue(!file_exists(__DIR__ . '/test.html'));
        file_put_contents(__DIR__ . '/test.html', $content);

        $keyToFile = $this->mocking();

        $callback = function ($filePath) use ($content) {
            //Assert
            $this->assertTrue(is_file($filePath));
            $this->assertSame(realpath(__DIR__ . '/test.html') ,realpath($filePath));
            $this->assertSame(substr($filePath, -5), '.html');
            $this->assertEquals($content, file_get_contents($filePath));
        };

        //Act
        $keyToFile->keyToFileForProcess(__DIR__, $key, $callback, 'html');
        $keyToFile->keyToFileForProcess(__DIR__, $key, $callback, '.html');
    }

    public function fileKeyDataProvider()
    {
        return [
            ['test'], ['test.html'], ['@test'], ['@test.html'],
            ['@.:.:..:' . basename(__DIR__) . ':test'],
            ['/././../' . basename(__DIR__) . '/test'],
        ];
    }

    protected function mocking()
    {
        $keyToFile = new class ()
        {
            use KeyToFileTrait {
                keyToFileForProcess as public;
            }

        };

        return $keyToFile;
    }
}
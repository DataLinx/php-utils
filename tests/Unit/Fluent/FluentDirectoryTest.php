<?php

declare(strict_types=1);

namespace DataLinx\PhpUtils\Tests\Unit\Fluent;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

require "./src/fluent_helpers.php";

/**
 * @covers \DataLinx\PhpUtils\Fluent\FluentDirectory
 */
class FluentDirectoryTest extends TestCase
{
    private static string $work_dir = 'build/directory_test';

    private static array $test_contents = [
        'test_dir_1',
        'test_dir_1/second_test.md',
        'test_dir_2',
        'test_dir_2/sub_dir_1',
        'test_dir_2/sub_dir_2',
        'test_dir_2/sub_dir_2/nested_dir_A',
        'test_dir_2/sub_dir_2/nested_dir_B',
        'test_dir_2/sub_dir_2/nested_dir_B/final_test.md',
        'test_dir_2/sub_dir_2/just_test.md',
        'test_dir_2/sub_dir_2/one_more_test.md',
        'test_dir_2/sub_dir_3',
        'test_dir_2/another_test.md',
        'test_dir_2/yet_another_test.md',
        'test_dir_3',
        'test_file.md',
        'test_file_2.md',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // Make sure test directories and files exist
        if (! file_exists(self::$work_dir)) {
            mkdir(self::$work_dir, 0777, true);
        }

        foreach (self::$test_contents as $item) {
            $item_path = self::$work_dir .'/'. $item;
            if (! file_exists($item_path)) {
                if (stripos($item_path, '.md') > 0) {
                    // It's a file
                    touch($item_path);
                } else {
                    // It's a dir
                    mkdir($item_path);
                }
            }
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Delete any existing files and directories after tests are done
        foreach (array_reverse(self::$test_contents) as $item) {
            $item_path = self::$work_dir .'/'. $item;
            if (file_exists($item_path)) {
                if (is_dir($item_path)) {
                    rmdir($item_path);
                } else {
                    unlink($item_path);
                }
            }
        }

        if (file_exists(self::$work_dir)) {
            rmdir(self::$work_dir);
        }
    }

    /**
     * @return void
     */
    public function testSetAndGet(): void
    {
        $dir = directory(self::$work_dir);

        $this->assertEquals(self::$work_dir, $dir->getPath());
    }

    /**
     * @return void
     */
    public function testNonExistingPath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Path "some_non_existing_path" does not exist!');

        directory('some_non_existing_path');
    }

    /**
     * @return void
     */
    public function testInvalidPath(): void
    {
        // Use an existing file
        $file = self::$work_dir . DIRECTORY_SEPARATOR . 'test_dir_1/second_test.md';

        $this->expectExceptionMessage(sprintf('Path "%s" is not a directory!', $file));

        directory($file);
    }

    /**
     * @return void
     */
    public function testContentList(): void
    {
        $dir = directory(self::$work_dir);

        $this->assertEquals([
            'test_dir_1',
            'test_dir_2',
            'test_dir_3',
            'test_file.md',
            'test_file_2.md',
        ], $dir->getContentList());
    }

    /**
     * @return void
     */
    public function testRecursiveContentList(): void
    {
        $dir = directory(self::$work_dir);

        $this->assertEquals(self::$test_contents, $dir->getContentList(true));
    }

    /**
     * @return void
     */
    public function testClear(): void
    {
        $dir = directory(self::$work_dir);

        $dir->clear();

        foreach (self::$test_contents as $item) {
            $this->assertFileDoesNotExist(self::$work_dir . DIRECTORY_SEPARATOR . $item);
        }
    }

    /**
     * @return void
     */
    public function testDelete(): void
    {
        $dir = directory(self::$work_dir);

        $dir->delete();

        $this->assertDirectoryDoesNotExist(self::$work_dir);
    }
}

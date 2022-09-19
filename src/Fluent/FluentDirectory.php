<?php

namespace DataLinx\PhpUtils\Fluent;

use InvalidArgumentException;

class FluentDirectory
{
    /**
     * @var string The path to the directory
     */
    protected string $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        if (! file_exists($path)) {
            throw new InvalidArgumentException(sprintf('Path "%s" does not exist!', $path));
        }

        if (! is_dir($path)) {
            throw new InvalidArgumentException(sprintf('Path "%s" is not a directory!', $path));
        }

        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get directory contents (files and other subdirectories)
     *
     * @param bool $recursive Also include any nested directories and files
     * @return array
     */
    public function getContentList(bool $recursive = false): array
    {
        $dirs = [];
        $files = [];

        foreach (scandir($this->path) as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $item_path = $this->path . DIRECTORY_SEPARATOR . $item;

            if (is_dir($item_path)) {
                $dirs[] = $item;

                if ($recursive) {
                    $dir = new static($item_path);

                    foreach ($dir->getContentList(true) as $nested_item) {
                        $dirs[] = $item . DIRECTORY_SEPARATOR . $nested_item;
                    }
                }
            } else {
                $files[] = $item;
            }
        }

        sort($files);

        return array_merge($dirs, $files);
    }

    /**
     * Clear the directory - delete all contents, but not the directory itself
     *
     * @return void
     */
    public function clear(): void
    {
        foreach (array_reverse($this->getContentList(true)) as $item) {
            $item_path = $this->path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($item_path)) {
                rmdir($item_path);
            } else {
                unlink($item_path);
            }
        }
    }

    /**
     * Delete directory with all contents
     *
     * @return void
     */
    public function delete(): void
    {
        $this->clear();
        rmdir($this->path);
    }
}

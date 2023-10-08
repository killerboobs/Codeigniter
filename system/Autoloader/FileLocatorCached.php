<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Autoloader;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Cache\FactoriesCache\FileVarExportHandler;

/**
 * FileLocator with Cache
 *
 * There is no FileLocator interface, so this extends FileLocator.
 *
 * @see \CodeIgniter\Autoloader\FileLocatorCachedTest
 */
final class FileLocatorCached extends FileLocator
{
    private FileLocator $locator;

    /**
     * @var CacheInterface|FileVarExportHandler
     */
    private $cacheHandler;

    /**
     * Cache data
     *
     * [method => data]
     * E.g.,
     * [
     *     'search' => [$path => $foundPaths],
     * ]
     */
    private array $cache = [];

    /**
     * Is the cache updated?
     */
    private bool $cacheUpdated = false;

    private string $cacheKey = 'FileLocatorCache';

    /**
     * @param CacheInterface|FileVarExportHandler|null $cache
     */
    public function __construct(FileLocator $locator, $cache = null)
    {
        // Do not call parent constructor.

        $this->cacheHandler = $cache ?? new FileVarExportHandler();
        $this->locator      = $locator;

        $this->loadCache();
    }

    private function loadCache(): void
    {
        $data = $this->cacheHandler->get($this->cacheKey);

        if (is_array($data)) {
            $this->cache = $data;
        }
    }

    public function __destruct()
    {
        $this->saveCache();
    }

    private function saveCache(): void
    {
        if ($this->cacheUpdated) {
            $this->cacheHandler->save($this->cacheKey, $this->cache, 3600 * 24);
        }
    }

    /**
     * Delete cache data
     */
    public function deleteCache(): void
    {
        $this->cacheHandler->delete($this->cacheKey);
    }

    protected function getNamespaces()
    {
        if (isset($this->cache['getNamespaces'])) {
            return $this->cache['getNamespaces'];
        }

        $namespaces = $this->locator->getNamespaces();

        $this->cache['getNamespaces'] = $namespaces;
        $this->cacheUpdated           = true;

        return $namespaces;
    }

    public function findQualifiedNameFromPath(string $path): false|string
    {
        if (isset($this->cache['findQualifiedNameFromPath'][$path])) {
            return $this->cache['findQualifiedNameFromPath'][$path];
        }

        $classname = $this->locator->findQualifiedNameFromPath($path);

        $this->cache['findQualifiedNameFromPath'][$path] = $classname;
        $this->cacheUpdated                              = true;

        return $classname;
    }

    public function getClassname(string $file): string
    {
        if (isset($this->cache['getClassname'][$file])) {
            return $this->cache['getClassname'][$file];
        }

        $classname = $this->locator->getClassname($file);

        $this->cache['getClassname'][$file] = $classname;
        $this->cacheUpdated                 = true;

        return $classname;
    }

    public function search(string $path, string $ext = 'php', bool $prioritizeApp = true): array
    {
        if (isset($this->cache['search'][$path][$ext][$prioritizeApp])) {
            return $this->cache['search'][$path][$ext][$prioritizeApp];
        }

        $foundPaths = $this->locator->search($path, $ext, $prioritizeApp);

        $this->cache['search'][$path][$ext][$prioritizeApp] = $foundPaths;
        $this->cacheUpdated                                 = true;

        return $foundPaths;
    }

    public function listFiles(string $path): array
    {
        if (isset($this->cache['listFiles'][$path])) {
            return $this->cache['listFiles'][$path];
        }

        $files = $this->locator->listFiles($path);

        $this->cache['listFiles'][$path] = $files;
        $this->cacheUpdated              = true;

        return $files;
    }

    public function listNamespaceFiles(string $prefix, string $path): array
    {
        if (isset($this->cache['listNamespaceFiles'][$prefix][$path])) {
            return $this->cache['listNamespaceFiles'][$prefix][$path];
        }

        $files = $this->locator->listNamespaceFiles($prefix, $path);

        $this->cache['listNamespaceFiles'][$prefix][$path] = $files;
        $this->cacheUpdated                                = true;

        return $files;
    }

    public function locateFile(string $file, ?string $folder = null, string $ext = 'php')
    {
        if (isset($this->cache['locateFile'][$file][$folder][$ext])) {
            return $this->cache['locateFile'][$file][$folder][$ext];
        }

        $files = $this->locator->locateFile($file, $folder, $ext);

        $this->cache['locateFile'][$file][$folder][$ext] = $files;
        $this->cacheUpdated                              = true;

        return $files;
    }
}
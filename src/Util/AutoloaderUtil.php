<?php declare(strict_types=1);

namespace Surda\Maker\Util;

use Composer\Autoload\ClassLoader;

class AutoloaderUtil
{
    /**
     * @var ComposerAutoloaderFinder
     */
    private $autoloaderFinder;

    public function __construct(ComposerAutoloaderFinder $autoloaderFinder)
    {
        $this->autoloaderFinder = $autoloaderFinder;
    }

    /**
     * Returns the relative path to where a new class should live.
     *
     * @return string|null
     * @throws \Exception
     */
    public function getPathForFutureClass(string $className): ?string
    {
        $classLoader = $this->getClassLoader();

        // lookup is obviously modeled off of Composer's autoload logic
        foreach ($classLoader->getPrefixesPsr4() as $prefix => $paths) {
            if (0 === strpos($className, $prefix)) {
                return $paths[0] . '/' . str_replace('\\', '/', substr($className, \strlen($prefix))) . '.php';
            }
        }

        foreach ($classLoader->getPrefixes() as $prefix => $paths) {
            if (0 === strpos($className, $prefix)) {
                return $paths[0] . '/' . str_replace('\\', '/', $className) . '.php';
            }
        }

        if ($classLoader->getFallbackDirsPsr4()) {
            return $classLoader->getFallbackDirsPsr4()[0] . '/' . str_replace('\\', '/', $className) . '.php';
        }

        if ($classLoader->getFallbackDirs()) {
            return $classLoader->getFallbackDirs()[0] . '/' . str_replace('\\', '/', $className) . '.php';
        }

        return NULL;
    }

    /**
     * @param string $className
     * @return string
     */
    public function getNamespacePrefixForClass(string $className): string
    {
        foreach ($this->getClassLoader()->getPrefixesPsr4() as $prefix => $paths) {
            if (0 === strpos($className, $prefix)) {
                return $prefix;
            }
        }

        return '';
    }

    /**
     * Returns if the namespace is configured by composer autoloader.
     *
     * @param string $namespace
     * @return bool
     */
    public function isNamespaceConfiguredToAutoload(string $namespace): bool
    {
        $namespace = trim($namespace, '\\') . '\\';
        $classLoader = $this->getClassLoader();

        foreach ($classLoader->getPrefixesPsr4() as $prefix => $paths) {
            if (0 === strpos($namespace, $prefix)) {
                return TRUE;
            }
        }

        foreach ($classLoader->getPrefixes() as $prefix => $paths) {
            if (0 === strpos($namespace, $prefix)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * @return ClassLoader
     * @throws \Exception
     */
    private function getClassLoader(): ClassLoader
    {
        return $this->autoloaderFinder->getClassLoader();
    }
}
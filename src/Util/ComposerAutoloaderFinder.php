<?php declare(strict_types=1);

namespace Surda\Maker\Util;

use Composer\Autoload\ClassLoader;

/**
 * @internal
 */
class ComposerAutoloaderFinder
{
    /** @var array */
    private $rootNamespace;

    /** @var ClassLoader|null */
    private $classLoader = NULL;

    /**
     * @param string $rootNamespace
     */
    public function __construct(string $rootNamespace)
    {
        $this->rootNamespace = [
            'psr0' => rtrim($rootNamespace, '\\'),
            'psr4' => rtrim($rootNamespace, '\\') . '\\',
        ];
    }

    /**
     * @return ClassLoader
     * @throws \Exception
     */
    public function getClassLoader(): ClassLoader
    {
        if (NULL === $this->classLoader) {
            $this->classLoader = $this->findComposerClassLoader();
        }

        if (NULL === $this->classLoader) {
            throw new \Exception("Could not find a Composer autoloader that autoloads from '{$this->rootNamespace['psr4']}'");
        }

        return $this->classLoader;
    }

    /**
     * @return ClassLoader|null
     */
    private function findComposerClassLoader(): ?ClassLoader
    {
        $autoloadFunctions = spl_autoload_functions();

        if (is_iterable($autoloadFunctions)) {
            foreach ($autoloadFunctions as $autoloader) {
                if (!\is_array($autoloader)) {
                    continue;
                }

                $classLoader = $this->extractComposerClassLoader($autoloader);
                if (NULL === $classLoader) {
                    continue;
                }

                $finalClassLoader = $this->locateMatchingClassLoader($classLoader);
                if (NULL !== $finalClassLoader) {
                    return $finalClassLoader;
                }
            }
        }

        return NULL;
    }

    /**
     * @return ClassLoader|null
     */
    private function extractComposerClassLoader(array $autoloader): ?ClassLoader
    {
        if (isset($autoloader[0]) && \is_object($autoloader[0])) {
            if ($autoloader[0] instanceof ClassLoader) {
                return $autoloader[0];
            }
        }

        return NULL;
    }

    /**
     * @return ClassLoader|null
     */
    private function locateMatchingClassLoader(ClassLoader $classLoader): ?ClassLoader
    {
        $makerClassLoader = NULL;
        foreach ($classLoader->getPrefixesPsr4() as $prefix => $paths) {
            if ('Symfony\\Bundle\\MakerBundle\\' === $prefix) {
                $makerClassLoader = $classLoader;
            }
            if (0 === strpos($this->rootNamespace['psr4'], $prefix)) {
                return $classLoader;
            }
        }

        foreach ($classLoader->getPrefixes() as $prefix => $paths) {
            if (0 === strpos($this->rootNamespace['psr0'], $prefix)) {
                return $classLoader;
            }
        }

        // Nothing found? Try the class loader where we found MakerBundle
        return $makerClassLoader;
    }
}
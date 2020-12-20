<?php declare(strict_types=1);

namespace Surda\Maker;

use Nette\Utils\FileSystem;
use Surda\Maker\Exception\RuntimeCommandException;
use Surda\Maker\Util\AutoloaderUtil;
use Surda\Maker\Util\Str;

class FileManager
{
    /** @var AutoloaderUtil */
    private $autoloaderUtil;

    /** @var string */
    private $rootDirectory;

    /** @var string */
    private $templateDirectory;

    /**
     * @param AutoloaderUtil $autoloaderUtil
     * @param string         $rootDirectory
     * @param string         $templateDirectory
     * @throws \Exception
     */
    public function __construct(AutoloaderUtil $autoloaderUtil, string $rootDirectory, string $templateDirectory)
    {
        $this->autoloaderUtil = $autoloaderUtil;
        $this->rootDirectory = rtrim($this->realPath($this->normalizeSlashes($rootDirectory)), '/');
        $this->templateDirectory = rtrim($this->relativizePath($templateDirectory), '/');
    }

    /**
     * @param string $className
     * @param string $content
     * @return string
     * @throws \Exception
     */
    public function writeContent(string $className, string $content): string
    {
        try {
            $targetPath = $this->getRelativePathForFutureClass($className);
        }
        catch (RuntimeCommandException|\Exception $e) {
            throw new \LogicException(sprintf('Could not determine where to locate the new class "%s", maybe try with a full namespace like "\\My\\Full\\Namespace\\%s"', $className, Str::getShortClassName($className)));
        }

        if ($this->fileExists($targetPath)) {
            throw new RuntimeCommandException(sprintf('The file "%s" can\'t be generated because it already exists.', $this->relativizePath($targetPath)));
        }

        $absolutePath = $this->absolutizePath($targetPath);

        $this->write($absolutePath, $content);

        return $targetPath;
    }

    /**
     * @param string $file
     * @param string $content
     * @throws \Exception
     */
    private function write(string $file, string $content): void
    {
        FileSystem::createDir(dirname($file), 0775);
        FileSystem::write($file, $content, 0664);
    }

    /**
     * @param string $path
     * @return bool
     */
    public function fileExists(string $path): bool
    {
        return file_exists($this->absolutizePath($path));
    }

    /**
     * @param string $absolutePath
     * @return string
     * @throws \Exception
     */
    public function relativizePath(string $absolutePath): string
    {
        $absolutePath = $this->normalizeSlashes($absolutePath);

        // see if the path is even in the root
        if (FALSE === strpos($absolutePath, $this->rootDirectory)) {
            return $absolutePath;
        }

        $absolutePath = $this->realPath($absolutePath);

        // str_replace but only the first occurrence
        $relativePath = ltrim(
            implode(
                '',
                is_iterable($arr = explode($this->rootDirectory, $absolutePath, 2)) ? $arr : []
            ),
            '/'
        );
        if (0 === strpos($relativePath, './')) {
            $relativePath = substr($relativePath, 2);
        }

        return is_dir($absolutePath) ? rtrim($relativePath, '/') . '/' : $relativePath;
    }

    /**
     * @param string $path
     * @return string
     */
    public function absolutizePath(string $path): string
    {
        if (0 === strpos($path, '/')) {
            return $path;
        }

        // support windows drive paths: C:\ or C:/
        if (1 === strpos($path, ':\\') || 1 === strpos($path, ':/')) {
            return $path;
        }

        return sprintf('%s/%s', $this->rootDirectory, $path);
    }

    /**
     * @param string $className
     * @return string
     * @throws RuntimeCommandException|\Exception
     */
    public function getRelativePathForFutureClass(string $className): string
    {
        $path = $this->autoloaderUtil->getPathForFutureClass($className);

        if ($path === NULL) {
            throw new RuntimeCommandException('Path is NULL');
        }

        return $this->relativizePath($path);
    }

    public function getNamespacePrefixForClass(string $className): string
    {
        return $this->autoloaderUtil->getNamespacePrefixForClass($className);
    }

    public function getPathForTemplate(string $filename): string
    {
        if (NULL === $this->templateDirectory) {
            throw new \RuntimeException('Cannot get path for template: is Twig installed?');
        }

        return $this->templateDirectory . '/' . $filename;
    }

    /**
     * @param string $absolutePath
     * @return string
     * @throws \Exception
     */
    private function realPath(string $absolutePath): string
    {
        $finalParts = [];
        $currentIndex = -1;

        $absolutePath = $this->normalizeSlashes($absolutePath);
        foreach (explode('/', $absolutePath) as $pathPart) {
            if ('..' === $pathPart) {
                // we need to remove the previous entry
                if (-1 === $currentIndex) {
                    throw new \Exception(sprintf('Problem making path relative - is the path "%s" absolute?', $absolutePath));
                }

                unset($finalParts[$currentIndex]);
                --$currentIndex;

                continue;
            }

            $finalParts[] = $pathPart;
            ++$currentIndex;
        }

        $finalPath = implode('/', $finalParts);
        // Normalize: // => /
        // Normalize: /./ => /
        $finalPath = str_replace(['//', '/./'], '/', $finalPath);

        return $finalPath;
    }

    /**
     * @param string $path
     * @return string
     */
    private function normalizeSlashes(string $path): string
    {
        return str_replace('\\', '/', $path);
    }
}
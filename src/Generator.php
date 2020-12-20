<?php declare(strict_types=1);

namespace Surda\Maker;

use Surda\Maker\Exception\RuntimeCommandException;
use Surda\Maker\Util\ClassNameDetails;
use Surda\Maker\Util\Str;

class Generator
{
    /** @var FileManager */
    private $fileManager;

    /** @var string */
    private $namespacePrefix;

    /**
     * @param FileManager $fileManager
     * @param string      $namespacePrefix
     */
    public function __construct(FileManager $fileManager, string $namespacePrefix)
    {
        $this->fileManager = $fileManager;
        $this->namespacePrefix = trim($namespacePrefix, '\\');
    }

    /**
     * Generate a new file for a class from a template.
     *
     * @param string $className    The fully-qualified class name
     * @param string $templatePath Template name in Resources/skeleton to use
     * @param array  $variables    Array of variables to pass to the template
     *
     * @return string The path where the file will be created
     *
     * @throws \Exception
     */
    public function generateClassContent(string $className, string $templatePath, array $variables = []): string
    {
        $absoluteTemplatePath = $this->fileManager->getPathForTemplate($templatePath);

        if (!file_exists($absoluteTemplatePath)) {
            throw new RuntimeCommandException(sprintf('The template "%s" not exists.', $templatePath));
        }

        $variables = array_merge($variables, [
            'class_name' => Str::getShortClassName($className),
            'namespace' => Str::getNamespace($className),
        ]);

        return (new \Latte\Engine)->renderToString($absoluteTemplatePath, $variables);
    }

    /**
     * Creates a helper object to get data about a class name.
     *
     * Examples:
     *
     *      // App\Entity\FeaturedProduct
     *      $gen->createClassNameDetails('FeaturedProduct', 'Entity');
     *      $gen->createClassNameDetails('featured product', 'Entity');
     *
     *      // App\Controller\FooController
     *      $gen->createClassNameDetails('foo', 'Controller', 'Controller');
     *
     *      // App\Controller\Admin\FooController
     *      $gen->createClassNameDetails('Foo\\Admin', 'Controller', 'Controller');
     *
     *      // App\Controller\Security\Voter\CoolController
     *      $gen->createClassNameDetails('Cool', 'Security\Voter', 'Voter');
     *
     *      // Full class names can also be passed. Imagine the user has an autoload
     *      // rule where Cool\Stuff lives in a "lib/" directory
     *      // Cool\Stuff\BalloonController
     *      $gen->createClassNameDetails('Cool\\Stuff\\Balloon', 'Controller', 'Controller');
     *
     * @param string $name            The short "name" that will be turned into the class name
     * @param string $namespacePrefix Recommended namespace where this class should live, but *without* the "App\\" part
     * @param string $suffix          Optional suffix to guarantee is on the end of the class
     */
    public function createClassNameDetails(string $name, string $namespacePrefix, string $suffix = '', string $validationErrorMessage = ''): ClassNameDetails
    {
        $name = Str::normalizeClassName($name);

        $fullNamespacePrefix = $this->namespacePrefix . '\\' . $namespacePrefix;
        if ('\\' === $name[0]) {
            // class is already "absolute" - leave it alone (but strip opening \)
            $className = substr($name, 1);
        } else {
            $className = rtrim($fullNamespacePrefix, '\\') . '\\' . Str::asClassName($name, $suffix);
        }

        Validator::validateClassName($className, $validationErrorMessage);

        // if this is a custom class, we may be completely different than the namespace prefix
        // the best way can do, is find the PSR4 prefix and use that
        if (0 !== strpos($className, $fullNamespacePrefix)) {
            $fullNamespacePrefix = $this->fileManager->getNamespacePrefixForClass($className);
        }

        return new ClassNameDetails($className, $fullNamespacePrefix, $suffix);
    }
}
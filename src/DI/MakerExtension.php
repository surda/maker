<?php declare(strict_types=1);

namespace Surda\Maker\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Surda\Maker\Command\MakeEntityCommand;
use Surda\Maker\FileManager;
use Surda\Maker\Generator;
use Surda\Maker\Maker\EntityMaker;
use Surda\Maker\Util\AutoloaderUtil;
use Surda\Maker\Util\ComposerAutoloaderFinder;
use stdClass;

/**
 * @property-read stdClass $config
 */
class MakerExtension extends CompilerExtension
{
    public function getConfigSchema(): Schema
    {
        $parameters = $this->getContainerBuilder()->parameters;
        $appDir = array_key_exists('appDir', $parameters) ? $parameters['appDir'] : NULL;

        $entity = Expect::structure([
            'namespacePrefix' => Expect::string()->default('Model'),
        ]);

        return Expect::structure([
            'core' => Expect::structure([
                'rootNamespace' => Expect::string()->default('App'),
                'rootDirectory' => Expect::string()->default((string) realpath($appDir . '/..')),
                'templateDirectory' => Expect::string()->default((string) realpath(__DIR__ .  '/../Resources')),
            ]),
            'entity' => Expect::anyOf(FALSE, $entity)->default($entity),
        ]);
    }

    public function loadConfiguration(): void
    {
        $config = $this->config;

        $builder = $this->getContainerBuilder();

        $builder->addDefinition($this->prefix('composerAutoloaderFinder'))
            ->setFactory(ComposerAutoloaderFinder::class, [
                $config->core->rootNamespace,
            ]);

        $builder->addDefinition($this->prefix('autoloaderUtil'))
            ->setFactory(AutoloaderUtil::class, [
                $this->prefix('@composerAutoloaderFinder'),
            ]);

        $builder->addDefinition($this->prefix('fileManager'))
            ->setFactory(FileManager::class, [
                $this->prefix('@autoloaderUtil'),
                $config->core->rootDirectory,
                $config->core->templateDirectory,
            ]);

        $builder->addDefinition($this->prefix('generator'))
            ->setFactory(Generator::class, [
                $this->prefix('@fileManager'),
                $config->core->rootNamespace,
            ]);

        if ($config->entity !== FALSE) {
            $builder->addDefinition($this->prefix('entity.maker'))
                ->setFactory(EntityMaker::class, [
                    $this->prefix('@generator'),
                    $this->prefix('@fileManager'),
                ]);

            $builder->addDefinition($this->prefix('entity.command'))
                ->setFactory(MakeEntityCommand::class, [
                    $this->prefix('@entity.maker'),
                    $this->prefix('@fileManager'),
                ]);
        }
    }
}
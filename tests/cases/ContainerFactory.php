<?php declare(strict_types=1);

namespace Tests\Surda\Maker;

use Nette\DI\Compiler;
use Nette\DI\Container;
use Nette\DI\ContainerLoader;
use Surda\Maker\DI\MakerExtension;

class ContainerFactory
{
    /**
     * @param array $config
     * @param mixed $key
     * @return Container
     */
    public function create(array $config, $key = NULL): Container
    {
        $loader = new ContainerLoader(TEMP_DIR, TRUE);
        $class = $loader->load(function (Compiler $compiler) use ($config): void {
            $compiler->addConfig($config);
            $compiler->addExtension('maker', new MakerExtension());
        }, $key);

        return new $class();
    }
}
<?php declare(strict_types=1);

namespace Tests\Surda\Maker\DI;

use Nette\DI\Container;
use Surda\Maker\Generator;
use Tester\Assert;
use Tester\TestCase;
use Tests\Surda\Maker\ContainerFactory;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class MakerExtensionTest extends TestCase
{
    public function testRegistration()
    {
        /** @var Container $container */
        $container = (new ContainerFactory())->create([
            'maker' => [
                'core' => [
                    'rootNamespace' => 'App',
                    'rootDirectory' => __DIR__,
                    'templateDirectory' => __DIR__,
                ],
                'entity' => FALSE,
            ],
        ]);

        /** @var Generator $generator */
        $generator = $container->getService('maker.generator');
//        Assert::true(true);
        Assert::true($generator instanceof Generator);

//        /** @var MpdfFactory $factory */
//        $generator = $container->getByType(MpdfFactory::class);
//        Assert::true($generator instanceof MpdfFactory);
    }
}

(new MakerExtensionTest())->run();
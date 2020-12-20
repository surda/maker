<?php declare(strict_types=1);

namespace Tests\Surda\Maker\Util;

use Surda\Maker\Util\ClassNameDetails;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class ClassNameDetailsTest extends TestCase
{
    public function testClassNameDetails()
    {
        $object = new ClassNameDetails('App\\Entity\\Foo\\BarCommand', 'App\\Entity', 'Command');
        Assert::same('App\Entity\Foo', $object->getNamespace());
        Assert::same('App\Entity\Foo\BarCommand', $object->getFullName());
        Assert::same('BarCommand', $object->getShortName());
        Assert::same('Foo\Bar', $object->getRelativeNameWithoutSuffix());
        Assert::same('Foo\BarCommand', $object->getRelativeName());
    }
}

(new ClassNameDetailsTest())->run();
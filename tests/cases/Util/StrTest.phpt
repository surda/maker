<?php declare(strict_types=1);

namespace Tests\Surda\Maker\Util;

use Surda\Maker\Util\Str;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class StrTest extends TestCase
{
    /** @dataProvider provideHasSuffix */
    public function testHasSuffix($value, $suffix, $expectedResult)
    {
        Assert::same($expectedResult, Str::hasSuffix($value, $suffix));
    }

    public function provideHasSuffix()
    {
        yield ['', '', TRUE];
        yield ['GenerateCommand', '', FALSE];
        yield ['GenerateCommand', 'Command', TRUE];
        yield ['GenerateCommand', 'command', TRUE];
        yield ['Generatecommand', 'Command', TRUE];
        yield ['Generatecommand', 'command', TRUE];
        yield ['Generate', 'command', FALSE];
        yield ['Generate', 'Command', FALSE];
    }

    /** @dataProvider provideAddSuffix */
    public function testAddSuffix($value, $suffix, $expectedResult)
    {
        Assert::same($expectedResult, Str::addSuffix($value, $suffix));
    }

    public function provideAddSuffix()
    {
        yield ['', '', ''];
        yield ['GenerateCommand', '', 'GenerateCommand'];
        yield ['GenerateCommand', 'Command', 'GenerateCommandCommand'];
        yield ['GenerateCommand', 'command', 'GenerateCommandcommand'];
        yield ['Generatecommand', 'Command', 'GeneratecommandCommand'];
        yield ['Generatecommand', 'command', 'Generatecommandcommand'];
        yield ['GenerateCommandCommand', 'Command', 'GenerateCommandCommandCommand'];
        yield ['GenerateCommandcommand', 'Command', 'GenerateCommandcommandCommand'];
        yield ['Generate', 'command', 'Generatecommand'];
        yield ['Generate', 'Command', 'GenerateCommand'];
    }

    /** @dataProvider provideRemoveSuffix */
    public function testRemoveSuffix($value, $suffix, $expectedResult)
    {
        Assert::same($expectedResult, Str::removeSuffix($value, $suffix));
    }

    public function provideRemoveSuffix()
    {
        yield ['', '', ''];
        yield ['GenerateCommand', '', 'GenerateCommand'];
        yield ['GenerateCommand', 'Command', 'Generate'];
        yield ['GenerateCommand', 'command', 'Generate'];
        yield ['Generatecommand', 'Command', 'Generate'];
        yield ['Generatecommand', 'command', 'Generate'];
        yield ['GenerateCommandCommand', 'Command', 'GenerateCommand'];
        yield ['GenerateCommandcommand', 'Command', 'GenerateCommand'];
        yield ['Generate', 'Command', 'Generate'];
    }

    /** @dataProvider provideAsClassName */
    public function testAsClassName($value, $suffix, $expectedResult)
    {
        Assert::same($expectedResult, Str::asClassName($value, $suffix));
    }

    public function provideAsClassName()
    {
        yield ['', '', ''];
        yield ['GenerateCommand', '', 'GenerateCommand'];
        yield ['Generate Command', '', 'GenerateCommand'];
        yield ['Generate-Command', '', 'GenerateCommand'];
        yield ['Generate:Command', '', 'GenerateCommand'];
        yield ['gen-erate:Co-mman-d', '', 'GenErateCoMmanD'];
        yield ['generate', 'Command', 'GenerateCommand'];
        yield ['app:generate', 'Command', 'AppGenerateCommand'];
        yield ['app:generate:command', 'Command', 'AppGenerateCommandCommand'];
    }

    /** @dataProvider provideGetNamespace */
    public function testGetNamespace(string $fullClassName, string $expectedNamespace)
    {
        Assert::same($expectedNamespace, Str::getNamespace($fullClassName));
    }

    public function provideGetNamespace()
    {
        yield ['App\\Entity\\Foo', 'App\\Entity'];
        yield ['DateTime', ''];
    }

    /** @dataProvider provideNormalizeClassName */
    public function testNormalizeClassName(string $fullClassName, string $expectedNamespace)
    {
        Assert::same($expectedNamespace, Str::normalizeClassName($fullClassName));
    }

    public function provideNormalizeClassName()
    {
        yield ['app\\entity\\foo', 'App\\Entity\\Foo'];
        yield ['App\\entity\\foo', 'App\\Entity\\Foo'];
        yield ['App\\Entity\\foo', 'App\\Entity\\Foo'];
        yield ['App\\Entity\\Foo', 'App\\Entity\\Foo'];
        yield ['FooBar', 'FooBar'];
        yield ['fooBar', 'FooBar'];
    }

    /** @dataProvider provideGetShortClassName */
    public function testGetShortClassName(string $fullClassName, string $expectedNamespace)
    {
        Assert::same($expectedNamespace, Str::getShortClassName($fullClassName));
    }

    public function provideGetShortClassName()
    {
        yield ['App\\Entity\\Foo', 'Foo'];
        yield ['App\\Entity\\foo', 'foo'];
        yield ['Foo', 'Foo'];
    }
}

(new StrTest())->run();
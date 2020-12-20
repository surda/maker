<?php declare(strict_types=1);

namespace Tests\Surda\Maker;

use Surda\Maker\Validator;
use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class ValidatorTest extends TestCase
{
    public function testValidateClassName()
    {
        Assert::same('Foo', Validator::validateClassName('Foo'));
        Assert::same('App\Entity\Foo', Validator::validateClassName('App\Entity\Foo'));

        Assert::exception(function () {
            Validator::validateClassName('function');
        }, \Surda\Maker\Exception\RuntimeCommandException::class, '"function" is a reserved keyword and thus cannot be used as class name in PHP.');

        Assert::exception(function () {
            Validator::validateClassName('Foo\\function');
        }, \Surda\Maker\Exception\RuntimeCommandException::class, '"function" is a reserved keyword and thus cannot be used as class name in PHP.');

        Assert::exception(function () {
            Validator::validateClassName('1one');
        }, \Surda\Maker\Exception\RuntimeCommandException::class, '"1one" is not valid as a PHP class name (it must start with a letter or underscore, followed by any number of letters, numbers, or underscores)');

        Assert::exception(function () {
            Validator::validateClassName('1one', 'CustomErrorMessage');
        }, \Surda\Maker\Exception\RuntimeCommandException::class, 'CustomErrorMessage');

//        Assert::exception(function () {
//            Validator::validateClassName('Foo \x00 Bar');
//        }, \Surda\Maker\Exception\RuntimeCommandException::class, '"Foo \x00 Bar" is not valid as a PHP class name (it must start with a letter or underscore, followed by any number of letters, numbers, or underscores)');
//
//        Assert::exception(function () {
//            Validator::validateClassName("Foo\xFF");
//        }, \Surda\Maker\Exception\RuntimeCommandException::class, "\"Foo\xFF\" is not a UTF-8-encoded string.");
    }

    public function testNotBlank()
    {
        Assert::same('Foo', Validator::notBlank('Foo'));

        Assert::exception(function () {
            Validator::notBlank('');
        }, \Surda\Maker\Exception\RuntimeCommandException::class, 'This value cannot be blank.');

        Assert::exception(function () {
            Validator::notBlank(NULL);
        }, \Surda\Maker\Exception\RuntimeCommandException::class, 'This value cannot be blank.');
    }
}

(new ValidatorTest())->run();
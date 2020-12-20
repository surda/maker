<?php declare(strict_types=1);

namespace Tests\Surda\Maker\Exception;

use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/../../bootstrap.php';

/**
 * @testCase
 */
class RuntimeCommandExceptionTest extends TestCase
{
    public function testException()
    {
        Assert::exception(function () {
            throw new \Surda\Maker\Exception\RuntimeCommandException('Foo');
        }, \Surda\Maker\Exception\RuntimeCommandException::class, 'Foo');
    }
}

(new RuntimeCommandExceptionTest())->run();
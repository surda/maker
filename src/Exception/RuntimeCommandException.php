<?php declare(strict_types=1);

namespace Surda\Maker\Exception;

use Symfony\Component\Console\Exception\ExceptionInterface;

class RuntimeCommandException extends \RuntimeException implements ExceptionInterface
{

}
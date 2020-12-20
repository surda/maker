<?php declare(strict_types=1);

namespace Surda\Maker\Maker;

use Surda\Maker\ConsoleStyle;

interface MakerInterface
{
    public function make(string $name, ConsoleStyle $io): void;
}
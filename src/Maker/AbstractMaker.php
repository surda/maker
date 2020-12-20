<?php declare(strict_types=1);

namespace Surda\Maker\Maker;

use Surda\Maker\ConsoleStyle;

abstract class AbstractMaker implements MakerInterface
{
    /**
     * @param ConsoleStyle $io
     * @param string       $message
     */
    protected function writeCommentMessage(ConsoleStyle $io, string $message): void
    {
        $io->comment(sprintf(
            '%s: %s',
            '<fg=blue>created</>',
            $message
        ));
    }
}
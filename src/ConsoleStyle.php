<?php declare(strict_types=1);

namespace Surda\Maker;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ConsoleStyle extends SymfonyStyle
{
    /** @var OutputInterface */
    private $output;

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        parent::__construct($input, $output);
    }

//    /**
//     * @param array|string $message
//     */
//    public function success($message): void
//    {
//        $this->writeln('<fg=green;options=bold,underscore>OK</> ' . $message);
//    }
//
//    /**
//     * @param string|array $message
//     */
//    public function comment($message): void
//    {
//        $this->text($message);
//    }
//
//    /**
//     * @return OutputInterface
//     */
//    public function getOutput(): OutputInterface
//    {
//        return $this->output;
//    }
}
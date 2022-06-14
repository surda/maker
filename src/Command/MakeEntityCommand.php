<?php declare(strict_types=1);

namespace Surda\Maker\Command;

use Surda\Maker\ConsoleStyle;
use Surda\Maker\FileManager;
use Surda\Maker\Maker\MakerInterface;
use Surda\Maker\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeEntityCommand extends Command
{
    /** @var string|null */
    protected static $defaultName = 'make:entity';

    /** @var MakerInterface */
    private $maker;

    /** @var FileManager */
    private $fileManager;

    /** @var ConsoleStyle */
    private $io;

    /**
     * @param MakerInterface $maker
     * @param FileManager    $fileManager
     */
    public function __construct(MakerInterface $maker, FileManager $fileManager)
    {
        parent::__construct();

        $this->maker = $maker;
        $this->fileManager = $fileManager;
    }

    protected function configure(): void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Creates or updates a Doctrine entity class');
        $this->addArgument('name', InputArgument::OPTIONAL, sprintf('Class name of the entity to create or update (e.g. <fg=yellow>%s</>)', 'BookDir\Book'));
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new ConsoleStyle($input, $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output): void
    {

    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (NULL === $input->getArgument('name')) {
            $name = $this->io->ask(
                $this->getDefinition()->getArgument('name')->getDescription(),
                NULL,
                [Validator::class, 'notBlank']
            );
            $input->setArgument('name', $name);
        }

        $this->maker->make(
            is_iterable($input->getArgument('name')) ? '' : (string) $input->getArgument('name'),
            $this->io
        );

        $this->io->newLine();
        $this->io->writeln(' <bg=green;fg=black> Success! </>');
        $this->io->newLine();

        return 0;
    }
}
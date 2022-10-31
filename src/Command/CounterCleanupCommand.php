<?php

namespace App\Command;

use App\Repository\StatClientConnectionsRepository;
use Mpakfm\Printu;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CounterCleanupCommand extends Command
{
    const DAYS = 10;

    protected static $defaultName = 'app:counter:cleanup';
    protected static $defaultDescription = 'Removing statistics';
    protected $repo;

    public function __construct(string $name = null, StatClientConnectionsRepository $repository)
    {
        $this->repo = $repository;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('days', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
            ->addOption('m', '-m', InputOption::VALUE_NONE, 'Удалить все записи старше ХХ дней')
            ->addOption('p', '-p', InputOption::VALUE_NONE, 'Удалить все ping записи старше ХХ дней')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io   = new SymfonyStyle($input, $output);
        $days = $input->getArgument('days');

        if (!$days) {
            $days = self::DAYS;
            //$io->note(sprintf('You passed an argument: %s', $days));
        }
        $isOnlyPing = true;
        if ($input->getOption('m')) {
            $isOnlyPing = false;
        }
        Printu::info($days)->title('[CounterCleanupCommand] $days');
        Printu::info($isOnlyPing)->title('[CounterCleanupCommand] $isOnlyPing');

        //$io->success('You have a new command! Now make it your own! Pass --help to see your options.');
        $this->repo->deleteHistory($days, $isOnlyPing);

        return Command::SUCCESS;
    }
}

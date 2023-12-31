<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Processor\CommissionCalculationProcessorInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'commission:calculate',
    description: 'This command calculates the commission for every operation listed in the provided file.',
)]
class CommissionCalculateCommand extends Command
{
    public function __construct(
        private readonly CommissionCalculationProcessorInterface $commissionCalculationProcessor
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'filepath',
                InputArgument::REQUIRED,
                'Relative path to the file with operations.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->commissionCalculationProcessor->calculate($input->getArgument('filepath')) as $commission) {
            $output->writeln($commission);
        }

        return Command::SUCCESS;
    }
}

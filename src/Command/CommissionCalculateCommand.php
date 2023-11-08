<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\CommissionCalculation\CommissionCalculationService;
use App\Service\Reader\OperationReader;
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
        private readonly OperationReader $operationReader,
        private readonly CommissionCalculationService $commissionCalculationService
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
        $operations = $this->operationReader->getOperationsFromCSV($input->getArgument('filepath'));

        $commissions = $this->commissionCalculationService->getCommissionsForOperations($operations);

        $output->writeln($this->getCommissionOutputString($commissions));

        return Command::SUCCESS;
    }

    private function getCommissionOutputString(array $commissions): string
    {
        return implode("\n", $commissions);
    }
}

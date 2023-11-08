<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Service\CurrencyConverterProcessor;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CalculateCommissionCommandTest extends KernelTestCase
{
    private const COMMAND_NAME = 'commission:calculate';
    private const TEST_INPUT_FILEPATH = 'tests/Application/Fixture/test_input.csv';
    private const TEST_EXCHANGE_RATES = [
        'USD' => 1.1497,
        'JPY' => 129.53,
    ];

    private CommandTester $commandTester;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $command = $application->find(self::COMMAND_NAME);

        $testExchangeRatesResponse = [
            'date' => new \DateTimeImmutable(),
            'rates' => self::TEST_EXCHANGE_RATES,
        ];
        $currencyConverterMock = $this->createPartialMock(CurrencyConverterProcessor::class, ['getExchangeRates']);

        $currencyConverterMock
            ->expects($this->any())
            ->method('getExchangeRates')
            ->willReturn($testExchangeRatesResponse);

        $kernel->getContainer()->set(CurrencyConverterProcessor::class, $currencyConverterMock);

        $this->commandTester = new CommandTester($command);
    }

    public function testCalculateCommissionCommand(): void
    {
        $expectedOutput = file_get_contents(__DIR__.'/Fixture/test_expected_output.txt');

        $this->commandTester->execute(['filepath' => self::TEST_INPUT_FILEPATH]);

        $this->commandTester->assertCommandIsSuccessful();

        $actualOutput = $this->commandTester->getDisplay();

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}

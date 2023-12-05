<?php

declare(strict_types=1);

namespace App\Service\Processor;

use App\Enum\OperationCurrency;
use DateTimeImmutable;
use Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyConverterProcessor
{
    private array $exchangeRates;

    public function __construct(
        private readonly string $exchangeRatesApiUrl,
        private readonly HttpClientInterface $client
    ) {
        $this->exchangeRates = [];
    }

    /**
     * @throws Exception
     */
    public function getExchangeRates(): array
    {
        if (!$this->exchangeRatesAreValid()) {
            $this->setFreshExchangeRates();
        }

        return $this->exchangeRates;
    }

    public function convertToEur(float $amount, OperationCurrency $currency): float
    {
        if (!$this->currencyIsEur($currency)) {
            $exchangeRate = $this->getCurrencyExchangeRate($currency);

            return $amount / $exchangeRate;
        }

        return $amount;
    }

    public function convertFromEur(float $amount, OperationCurrency $currency): float
    {
        if (!$this->currencyIsEur($currency)) {
            $exchangeRate = $this->getCurrencyExchangeRate($currency);

            return $amount * $exchangeRate;
        }

        return $amount;
    }

    public function currencyIsEur(
        OperationCurrency $currency,
    ): bool {
        return $currency === OperationCurrency::EUR;
    }

    private function exchangeRatesAreValid(): bool
    {
        return $this->exchangeRates !== []
            && $this->exchangeRates['date'] === (new DateTimeImmutable())->format('Y-m-d');
    }

    private function setFreshExchangeRates(): void
    {
        try {
            $response = $this->client->request('GET', $this->exchangeRatesApiUrl);

            $this->exchangeRates = json_decode($response->getContent(), true);
        } catch (TransportExceptionInterface $e) {
            throw new Exception('There was an error while trying to get exchange rates from api!', $e->getCode());
        }
    }

    private function getCurrencyExchangeRate(OperationCurrency $currency): float
    {
        return $this->getExchangeRates()['rates'][$currency->value];
    }
}

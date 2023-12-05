<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\OperationCurrency;
use DateTimeImmutable;
use Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CurrencyConverterProcessor
{
    private const EXCHANGE_RATES_API = 'https://developers.paysera.com/tasks/api/currency-exchange-rates';

    private array $exchangeRates;

    public function __construct(private readonly HttpClientInterface $client)
    {
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
            $response = $this->client->request('GET', self::EXCHANGE_RATES_API);

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

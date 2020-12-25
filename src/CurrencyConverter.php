<?php


namespace CurrencyConverter;


use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\DTOs\Money;
use CurrencyConverter\Service\ICurrencyExchange;

class CurrencyConverter implements ICurrencyConverter
{
    protected ICurrencyExchange $exchangeService;

    /**
     * CurrencyConverter constructor.
     */
    public function __construct(ICurrencyExchange $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function convert(Money $money, Currency $to): Money {
        $ratio = $this->exchangeService->getRatio($money->getCurrency(), $to);
        $convertedAmount = $money->getAmount() * $ratio;
        return new Money($convertedAmount, $to);
    }
}
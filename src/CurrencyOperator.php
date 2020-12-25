<?php


namespace CurrencyConverter;


use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\DTOs\Money;

class CurrencyOperator implements ICurrencyOperator
{
    protected ICurrencyConverter $converter;

    /**
     * CurrencyOperator constructor.
     * @param ICurrencyConverter $converter
     */
    public function __construct(ICurrencyConverter $converter)
    {
        $this->converter = $converter;
    }

    public function sum(Money $money1, Money $money2, Currency $to) : Money {
        $convertedMoney1 = $this->converter->convert($money1, $to);
        $convertedMoney2 = $this->converter->convert($money2, $to);

        return new Money($convertedMoney1->getAmount() + $convertedMoney2->getAmount(), $to);
    }
}
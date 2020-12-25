<?php


namespace CurrencyConverter\DTOs;


class Currency
{
    const USD = 'USD';
    const ARS = 'ARS';

    protected string $currency;

    /**
     * Currency constructor.
     */
    public function __construct(string $currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}
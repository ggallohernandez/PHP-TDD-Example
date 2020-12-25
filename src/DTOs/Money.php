<?php


namespace CurrencyConverter\DTOs;


class Money
{
    protected Currency $currency;
    protected float $amount;


    /**
     * Money constructor.
     */
    public function __construct(float $amount, Currency $currency)
    {
        $this->currency = $currency;
        $this->amount = $amount;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }


}
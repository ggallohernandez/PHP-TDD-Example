<?php


namespace CurrencyConverter\Service;


use CurrencyConverter\DTOs\Currency;

class CurrencyExchangeService implements ICurrencyExchange
{
    protected ICurrencyExchange $strategy;

    /**
     * CurrencyExchangeService constructor.
     * @param ICurrencyExchange $strategy
     */
    public function __construct(ICurrencyExchange $strategy)
    {
        $this->strategy = $strategy;
    }

    public function getRatio(Currency $from, Currency $to) : float {
        return $this->strategy->getRatio($from, $to);
    }
}
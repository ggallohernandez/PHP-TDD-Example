<?php

namespace CurrencyConverter\Service;

use CurrencyConverter\DTOs\Currency;

interface ICurrencyExchange
{
    public function getRatio(Currency $from, Currency $to): float;
}
<?php

namespace CurrencyConverter;

use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\DTOs\Money;

interface ICurrencyConverter
{
    public function convert(Money $money, Currency $to): Money;
}
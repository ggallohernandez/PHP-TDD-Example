<?php

namespace CurrencyConverter;

use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\DTOs\Money;

interface ICurrencyOperator
{
    public function sum(Money $money1, Money $money2, Currency $to): Money;
}
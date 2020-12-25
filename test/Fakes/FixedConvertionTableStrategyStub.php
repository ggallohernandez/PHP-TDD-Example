<?php

namespace CurrencyConverterTest\Fakes;

use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\Exception\InvalidConvertionException;
use CurrencyConverter\Service\ICurrencyExchange;

class FixedConvertionTableStrategyStub implements ICurrencyExchange
{
    protected static array $convertionTable = [
        Currency::ARS => [Currency::USD => 0.012],
        Currency::USD => [Currency::ARS => 83.37],
    ];

    protected function isConvertionPosible(Currency $from, Currency $to): bool
    {
        return $from == $to ||
            array_key_exists($from->getCurrency(), self::$convertionTable) &&
            array_key_exists($to->getCurrency(), self::$convertionTable[$from->getCurrency()]);
    }

    public function getRatio(Currency $from, Currency $to): float
    {
        if ($from == $to)
            return 1;

        if (!$this->isConvertionPosible($from, $to))
            throw new InvalidConvertionException("Can't convert from {$from->getCurrency()} to {$to->getCurrency()}");

        return self::$convertionTable[$from->getCurrency()][$to->getCurrency()];
    }
}
<?php

namespace CurrencyConverterTest;

use CurrencyConverter\CurrencyConverter;
use CurrencyConverter\CurrencyOperator;
use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\DTOs\Money;
use CurrencyConverter\Service\CurrencyExchangeService;
use CurrencyConverterTest\Fakes\FixedConvertionTableStrategyStub;
use PHPUnit\Framework\TestCase;

class CurrencyOperatorTest extends TestCase
{

    public function testSum()
    {
        // Arrange
        $money1 = new Money(8337, new Currency(Currency::ARS));
        $money2 = new Money(100, new Currency(Currency::USD));
        $expectedMoney = new Money(16674, new Currency(Currency::ARS));
        $exchangeServiceStub = new CurrencyExchangeService(new FixedConvertionTableStrategyStub());
        $converter = new CurrencyConverter($exchangeServiceStub);
        $operator = new CurrencyOperator($converter);

        // Act
        $actualMoney = $operator->sum($money1, $money2, new Currency(Currency::ARS));

        // Assert
        $this->assertEquals($expectedMoney->getAmount(), $actualMoney->getAmount());
        $this->assertEquals($expectedMoney->getCurrency(), $actualMoney->getCurrency());
    }
}

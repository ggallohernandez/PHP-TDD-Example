<?php

namespace CurrencyConverterTest;

use CurrencyConverter\CurrencyConverter;
use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\DTOs\Money;
use CurrencyConverter\Exception\InvalidConvertionException;
use CurrencyConverter\Service\CurrencyExchangeService;
use CurrencyConverter\Service\ICurrencyExchange;
use CurrencyConverterTest\Fakes\FixedConvertionTableStrategyStub;
use PHPUnit\Framework\TestCase;


class CurrencyConverterTest extends TestCase
{
    public function testConvertARSToUSDReturnsUSD() {
        // Arrange
        $money = new Money(8337, new Currency(Currency::ARS));
        $expectedMoney = new Money(100, new Currency(Currency::USD));
        $exchangeServiceStub = new CurrencyExchangeService(new FixedConvertionTableStrategyStub());
        $converter = new CurrencyConverter($exchangeServiceStub);

        // Act
        $actualMoney = $converter->convert($money, new Currency(Currency::USD));

        // Assert
        $this->assertEquals($expectedMoney->getAmount(), round($actualMoney->getAmount()));
        $this->assertEquals($expectedMoney->getCurrency(), $actualMoney->getCurrency());
    }

    public function testConvertUSDToARSReturnsARS() {
        // Arrange
        $money = new Money(100, new Currency(Currency::USD));
        $expectedMoney = new Money(8337, new Currency(Currency::ARS));
        $exchangeServiceStub = new CurrencyExchangeService(new FixedConvertionTableStrategyStub());
        $converter = new CurrencyConverter($exchangeServiceStub);

        // Act
        $actualMoney = $converter->convert($money, new Currency(Currency::ARS));

        // Assert
        $this->assertEquals($expectedMoney->getAmount(), round($actualMoney->getAmount()));
        $this->assertEquals($expectedMoney->getCurrency(), $actualMoney->getCurrency());
    }

    public function testConvertMoneySameCurrecyReturnsSameAmount() {
        // Arrange
        $money = new Money(100, new Currency(Currency::ARS));
        $expectedMoney = new Money(100, new Currency(Currency::ARS));
        $exchangeServiceStub = new CurrencyExchangeService(new FixedConvertionTableStrategyStub());
        $converter = new CurrencyConverter($exchangeServiceStub);

        // Act
        $actualMoney = $converter->convert($money, $money->getCurrency());

        // Assert
        $this->assertEquals($expectedMoney->getAmount(), $actualMoney->getAmount());
        $this->assertEquals($expectedMoney->getCurrency(), $actualMoney->getCurrency());
    }

    public function testConvertMoneyUnhandledCurrecyThrowsInvalidConvertionException() {
        // Arrange
        $money = new Money(100, new Currency("GBP")); # Libras esterlinas
        $expectedMoney = new Money(11308.52, new Currency(Currency::ARS));
        $exchangeServiceStub = new CurrencyExchangeService(new FixedConvertionTableStrategyStub());
        $converter = new CurrencyConverter($exchangeServiceStub);

        $this->expectException(InvalidConvertionException::class);

        // Act
        $actualMoney = $converter->convert($money, new Currency(Currency::ARS));

        // Assert
        $this->assertNull($actualMoney);
    }
}


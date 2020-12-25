<?php

namespace CurrencyConverterTest\Service;

use CurrencyConverter\CurrencyConverter;
use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\DTOs\Money;
use CurrencyConverter\Service\CurrencyExchangeService;
use CurrencyConverterTest\Fakes\FixedConvertionTableStrategyStub;
use PHPUnit\Framework\TestCase;

class CurrencyExchangeServiceTest extends TestCase
{

    public function testGetRatio()
    {
        // Arrange
        $currency1 = new Currency(Currency::ARS);
        $currency2 = new Currency(Currency::USD);
        $expectedRatio = 0.012;
        $exchangeService = new CurrencyExchangeService(new FixedConvertionTableStrategyStub());

        // Act
        $actualRatio = $exchangeService->getRatio($currency1, $currency2);

        // Assert
        $this->assertEquals($expectedRatio, $actualRatio);
    }
}

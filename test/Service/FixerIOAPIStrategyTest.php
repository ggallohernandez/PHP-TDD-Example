<?php

namespace CurrencyConverterTest\Service;

use CurrencyConverter\CurrencyConverter;
use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\DTOs\Money;
use CurrencyConverter\Service\CurrencyExchangeService;
use CurrencyConverter\Service\FixerIOAPIStrategy;
use PHPUnit\Framework\TestCase;

class FixerIOAPIStrategyTest extends TestCase
{

    public function testGetRatioFromFixerIOAPIStrategy()
    {
        // Arrange
        $currency1 = new Currency(Currency::ARS);
        $currency2 = new Currency(Currency::USD);
        $exchangeService = new CurrencyExchangeService(new FixerIOAPIStrategy());

        // Act
        $actualRatio = $exchangeService->getRatio($currency1, $currency2);

        // Assert
        $this->assertIsFloat($actualRatio);
        $this->assertGreaterThan(0, $actualRatio);
        $this->assertLessThan(1, $actualRatio); # Fair assumption
    }
}

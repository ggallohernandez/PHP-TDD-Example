# PHP-TDD-Example
Example of a small currency exchange project, developed with TDD technique, KISS, DRY, and YAGNI principles, and a strategy design pattern.

# How to use it
Tests are always a good place for looking a how to use some library, but here is a small example.

```php
use CurrencyConverter\CurrencyConverter;
use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\DTOs\Money;
use CurrencyConverter\Service\CurrencyExchangeService;
use CurrencyConverter\Service\FixerIOAPIStrategy;

$money = new Money(8337, new Currency(Currency::ARS));
$exchangeServiceStub = new CurrencyExchangeService(new FixerIOAPIStrategy());
$converter = new CurrencyConverter($exchangeServiceStub);

$convertedMoney = $converter->convert($money, new Currency(Currency::USD));
```

# How to build
There are a few ways to do it; the easiest way is using docker-compose

```sh
$ docker-compose up -d
```

And then open in any browser the URL http://localhost:8080 to start playing with the Swagger UI

![Swagger UI](https://lh3.googleusercontent.com/c1iLxLhhi-jBZW1nYEuOBkGHVic4tDxLZbdImN90yD60aOq7o89gpkFSm5jZOolJmZVDKKSmIelLx67W_Q6Q-lVW1OZagUHLxOcX7tpazmoJc0ovvhxvhWtREk-Lg9s__IFEJGzmjg=w2400)

Other way it's usign just docker
```sh
$ export FIXERIO_API_KEY=952c91a77025e55b92678303102b99af # Or your fixer.io API KEY, you can use this free one for testing purposes.
$ docker build -t currency-converter .
$ docker run -d -p 8081:80 --name currency-converter-app currency-converter
```

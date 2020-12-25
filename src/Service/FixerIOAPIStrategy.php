<?php


namespace CurrencyConverter\Service;


use CurrencyConverter\DTOs\Currency;
use GuzzleHttp;

class FixerIOAPIStrategy implements ICurrencyExchange
{
    protected GuzzleHttp\Client $client;

    /**
     * DataFixerAPIStrategy constructor.
     */
    public function __construct()
    {
        $this->client = new GuzzleHttp\Client(['base_uri' => 'http://data.fixer.io/api/latest']);
    }

    public function getRatio(Currency $from, Currency $to): float
    {
        $params = [
            'access_key' => $_ENV['FIXERIO_API_KEY'],
            'symbols' => "{$from->getCurrency()},{$to->getCurrency()}",
        ];

        $response = $this->client->get('', ['query' => $params]);

        if ($response->getStatusCode() !== 200)
            throw new \RuntimeException($response);

        $body = json_decode($response->getBody());

        if (!$body->success)
            throw new \RuntimeException("{$body->error->code}: {$body->error->info}");

        return $body->rates->{$to->getCurrency()} / $body->rates->{$from->getCurrency()};
    }
}
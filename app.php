<?php declare(strict_types=1);

include 'vendor/autoload.php';

use CurrencyConverter\CurrencyConverter;
use CurrencyConverter\DTOs\Currency;
use CurrencyConverter\Service\CurrencyExchangeService;
use CurrencyConverterTest\Fakes\FixedConvertionTableStrategyStub;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use OpenApi\Annotations as OA;

class CORSMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler) : ResponseInterface
    {
        $response = $handler->handle($request);

        return $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', '*')
            ;
    }
}

class ExceptionMiddleware implements \Psr\Http\Server\MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, \Psr\Http\Server\RequestHandlerInterface $handler) : ResponseInterface
    {
        try {
            $response = $handler->handle($request);
        } catch (\CurrencyConverter\Exception\InvalidConvertionException $e) {
            $response = (new Laminas\Diactoros\Response())
                ->withStatus(422)
            ;

            $response->getBody()->write($e->getMessage());
        } catch (Throwable $e) {
            $response = (new Laminas\Diactoros\Response())
                ->withStatus(500)
            ;

            $response->getBody()->write($e->getMessage());
        } finally {
            return $response;
        }
    }
}

$request = Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
);

$responseFactory = new \Laminas\Diactoros\ResponseFactory();

$strategy = new League\Route\Strategy\JsonStrategy($responseFactory);
$router   = (new League\Route\Router)->setStrategy($strategy);
$router->middleware(new CORSMiddleware);
$router->middleware(new ExceptionMiddleware());

/**
 * @OA\Info(
 *   title="Currency Converter API",
 *   version="1.0.0",
 *   @OA\Contact(
 *     email="ggallohernandez@egmail.com"
 *   )
 * ),
 * @OA\Server(
 *   description="Currency Converter API",
 *   url="http://localhost:8081"
 * ),
 */
// map a route
$router->map('GET', '/swagger.json', function (ServerRequestInterface $request) : ResponseInterface  {
    $openapi = \OpenApi\scan(__DIR__, ['exclude' => ['.git', 'vendor', 'test']]);

    $response = new Laminas\Diactoros\Response();
    $response->getBody()->write($openapi->toJson());
    return $response;
});

/**
 * @OA\Get(
 *   path="/convert",
 *   summary="Convert from one currency to another.",
 *   @OA\Parameter(
 *     in="query",
 *     name="from",
 *     @OA\Schema(
 *        type="string"
 *     ),
 *     description="The currency ISO code of the amount to be converted",
 *     required=true
 *   ),
 *   @OA\Parameter(in="query",
 *     name="to",
 *     @OA\Schema(
 *        type="string"
 *     ),
 *     description="The currency ISO code of the result",
 *     required=true
 *   ),
 *   @OA\Parameter(in="query",
 *     name="amount",
 *     @OA\Schema(
 *        type="number",
 *        format="float",
 *     ),
 *     description="The amount to be converted",
 *     required=true
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="The converted amount"
 *   ),
 *   @OA\Response(
 *     response=422,
 *     description="Invalid convertion requested"
 *   ),
 *   @OA\Response(
 *     response=500,
 *     description="an ""unexpected"" error"
 *   )
 * )
 */
$router->map('GET', '/convert', function (ServerRequestInterface $request) : array {
    $params = $request->getQueryParams();

    if (!array_key_exists('from', $params))
        throw new \League\Route\Http\Exception\BadRequestException('The "from" parameter is required.');

    if (!array_key_exists('to', $params))
        throw new \League\Route\Http\Exception\BadRequestException('The "to" parameter is required.');

    if (!array_key_exists('amount', $params))
        throw new \League\Route\Http\Exception\BadRequestException('The "amount" parameter is required.');

    if (false === filter_var($params['amount'], FILTER_SANITIZE_NUMBER_FLOAT))
        throw new \League\Route\Http\Exception\BadRequestException('The "amount" must be a float number.');

    $amount = (float) filter_var($params['amount'], FILTER_SANITIZE_NUMBER_FLOAT);

    $money = new \CurrencyConverter\DTOs\Money($amount, new Currency($params['from']));
    $exchangeServiceStub = new CurrencyExchangeService(new FixedConvertionTableStrategyStub());
    $converter = new CurrencyConverter($exchangeServiceStub);

    // Act
    $result = $converter->convert($money, new Currency($params['to']));

    return ['result' => $result->getAmount()];
});

/**
 * @OA\Get(
 *   path="/sum",
 *   summary="Sum two currency.",
 *   @OA\Parameter(
 *     in="query",
 *     name="currency1",
 *     @OA\Schema(
 *        type="string"
 *     ),
 *     description="The currency ISO code",
 *     required=true
 *   ),
 *   @OA\Parameter(in="query",
 *     name="amount1",
 *     @OA\Schema(
 *        type="number",
 *        format="float",
 *     ),
 *     description="The amount parameter",
 *     required=true
 *   ),
 *   @OA\Parameter(in="query",
 *     name="currency2",
 *     @OA\Schema(
 *        type="string"
 *     ),
 *     description="The currency ISO code",
 *     required=true
 *   ),
 *   @OA\Parameter(in="query",
 *     name="amount2",
 *     @OA\Schema(
 *        type="number",
 *        format="float",
 *     ),
 *     description="The amount parameter",
 *     required=true
 *   ),
 *   @OA\Parameter(in="query",
 *     name="to",
 *     @OA\Schema(
 *        type="string"
 *     ),
 *     description="The currency ISO code of the result",
 *     required=true
 *   ),
 *   @OA\Response(
 *     response=200,
 *     description="The sum result"
 *   ),
 *   @OA\Response(
 *     response=422,
 *     description="Invalid convertion requested"
 *   ),
 *   @OA\Response(
 *     response=500,
 *     description="an ""unexpected"" error"
 *   )
 * )
 */
$router->map('GET', '/sum', function (ServerRequestInterface $request) : array {
    $params = $request->getQueryParams();

    if (!array_key_exists('currency1', $params))
        throw new \League\Route\Http\Exception\BadRequestException('The "currency1" parameter is required.');

    if (!array_key_exists('currency2', $params))
        throw new \League\Route\Http\Exception\BadRequestException('The "currency2" parameter is required.');

    if (!array_key_exists('to', $params))
        throw new \League\Route\Http\Exception\BadRequestException('The "to" parameter is required.');

    if (!array_key_exists('amount1', $params))
        throw new \League\Route\Http\Exception\BadRequestException('The "amount1" parameter is required.');

    if (false === filter_var($params['amount1'], FILTER_SANITIZE_NUMBER_FLOAT))
        throw new \League\Route\Http\Exception\BadRequestException('The "amount1" must be a float number.');

    $amount1 = (float) filter_var($params['amount1'], FILTER_SANITIZE_NUMBER_FLOAT);

    if (!array_key_exists('amount2', $params))
        throw new \League\Route\Http\Exception\BadRequestException('The "amount2" parameter is required.');

    if (false === filter_var($params['amount2'], FILTER_SANITIZE_NUMBER_FLOAT))
        throw new \League\Route\Http\Exception\BadRequestException('The "amount2" must be a float number.');

    $amount2 = (float) filter_var($params['amount2'], FILTER_SANITIZE_NUMBER_FLOAT);

    $money1 = new \CurrencyConverter\DTOs\Money($amount1, new Currency($params['currency1']));
    $money2 = new \CurrencyConverter\DTOs\Money($amount2, new Currency($params['currency2']));
    $exchangeServiceStub = new CurrencyExchangeService(new FixedConvertionTableStrategyStub());
    $converter = new CurrencyConverter($exchangeServiceStub);
    $operator = new \CurrencyConverter\CurrencyOperator($converter);

    // Act
    $result = $operator->sum($money1, $money2, new Currency($params['to']));

    return ['result' => $result->getAmount()];
});

$response = $router->dispatch($request);

// send the response to the browser
(new Laminas\HttpHandlerRunner\Emitter\SapiEmitter)->emit($response);
# http client

[![Latest Stable Version](https://poser.pugx.org/rokde/http-client/v/stable.svg)](https://packagist.org/packages/rokde/http-client) [![Latest Unstable Version](https://poser.pugx.org/rokde/http-client/v/unstable.svg)](https://packagist.org/packages/rokde/http-client) [![License](https://poser.pugx.org/rokde/http-client/license.svg)](https://packagist.org/packages/rokde/http-client) [![Total Downloads](https://poser.pugx.org/rokde/http-client/downloads.svg)](https://packagist.org/packages/rokde/http-client)

This http client library does not have any dependencies like curl or guzzle. It uses plain php functions to handle the
 most common http requests.

## Installation

	composer require rokde/http-client

## Usage

You have two options

1. Use the helper function `http()`
2. Use the full-blown class stack provided by the package

I recommend the use of the `http()` function to keep the packages purpose in mind: simplicity.

### Using `http()`

```php
// GET (get from uri)
/** @var string $response */
$response = http('https://httpbin.org/get');

// POST (post data to uri)
/** @var \Rokde\HttpClient\Response $response */
$response = http()->post(['data' => 'value'], 'https://httpbin.org/post');
```

### Using the class version

```php
$client = new \Rokde\HttpClient\Client();

$request = new \Rokde\HttpClient\Request('https://httpbin.org/get', 'GET', [
  'accept' => 'application/json',
  'x-verify-test' => 'true',
]);

$response = $client->send($request);

if ($response->isOk()) {
  $resultString = $response->content();
}
```

Further usage examples can be found at the `/tests` folder of this package.

### Testing

Run the tests with:

    composer test


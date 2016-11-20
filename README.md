# VMPro PHP API Client

[![Build Status](https://travis-ci.org/MovingImage24/VMProApiClient.svg?branch=master)](https://travis-ci.org/MovingImage24/VMProApiClient) [![Code Coverage](https://scrutinizer-ci.com/g/MovingImage24/VMProApiClient/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/MovingImage24/VMProApiClient/?branch=master)

## Installation

To install the API client, run the following command:

```
composer require movingimage/vmpro-api-client
```

## Usage

### With Guzzle 6

To use the VMPro API Client with Guzzle 6, you can use the factory like this:

```php
<?php

use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\ApiClientFactory;

require_once('./vendor/autoload.php');

$baseUri     = 'https://<api uri>';
$credentials = new ApiCredentials('<username>', '<password>');
$factory     = new ApiClientFactory();

$tokenManager    = $factory->createTokenManager($baseUri, $credentials);
$tokenMiddleware = $factory->createTokenMiddleware($tokenManager);
$httpClient      = $factory->createHttpClient($baseUri, [$tokenMiddleware]);

$apiClient = $factory->create($httpClient, $factory->createSerializer());

echo $apiClient->getChannels(5)->getName() . PHP_EOL;
```

### With Guzzle 5

To use the VMPro API Client with Guzzle 5, you can use the factory like this:

```php
<?php

use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\Factory\Guzzle5ApiClientFactory as ApiClientFactory;

require_once('./vendor/autoload.php');

$baseUri     = 'https://<api uri>';
$credentials = new ApiCredentials('<username>', '<password>');
$factory     = new ApiClientFactory();

$tokenManager    = $factory->createTokenManager($baseUri, $credentials);
$tokenSubscriber = $factory->createTokenSubscriber($tokenManager);
$httpClient      = $factory->createHttpClient($baseUri, [$tokenSubscriber]);

$apiClient = $factory->create($httpClient, $factory->createSerializer());

echo $apiClient->getChannels(5)->getName() . PHP_EOL;
```

## Maintainers

* Ruben Knol - ruben.knol@movingimage.com

If you have questions, suggestions or problems, feel free to get in touch with the maintainers by e-mail.

## Contributing

If you want to expand the functionality of the API clients, or fix a bug, feel free to fork and do a pull request back onto the 'master' branch.
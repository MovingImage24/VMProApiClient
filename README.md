# VMPro PHP API Client

[![Build Status](https://travis-ci.org/MovingImage24/VMProApiClient.svg?branch=master)](https://travis-ci.org/MovingImage24/VMProApiClient) [![Code Coverage](https://scrutinizer-ci.com/g/MovingImage24/VMProApiClient/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/MovingImage24/VMProApiClient/?branch=master) [![License](https://poser.pugx.org/movingimage/vmpro-api-client/license)](https://packagist.org/packages/movingimage/vmpro-api-client) [![Latest Unstable Version](https://poser.pugx.org/movingimage/vmpro-api-client/v/unstable)](https://packagist.org/packages/movingimage/vmpro-api-client) [![Latest Stable Version](https://poser.pugx.org/movingimage/vmpro-api-client/v/stable)](https://packagist.org/packages/movingimage/vmpro-api-client)

## Installation

To install the API client, run the following command:

```
composer require movingimage/vmpro-api-client
```

## Usage

To use the VMPro API Client, you can use the factory like this:

```php
<?php

use MovingImage\Client\VMPro\Entity\ApiCredentials;
use MovingImage\Client\VMPro\ApiClientFactory;

require_once('./vendor/autoload.php');

$baseUri     = 'https://<api uri>';
$credentials = new ApiCredentials('<username>', '<password>');
$factory     = new ApiClientFactory();

$apiClient = $factory->createSimple($baseUri, $credentials);

echo $apiClient->getChannels(5)->getName() . PHP_EOL;
```

If you use Guzzle 5, make sure to use `MovingImage\Client\VMPro\ApiClientFactory\Guzzle5ApiClientFactory` instead of `ApiClientFactory`.

## Maintainers

* Ruben Knol - ruben.knol@movingimage.com
* Omid Rad - omid.rad@movingimage.com

If you have questions, suggestions or problems, feel free to get in touch with the maintainers by e-mail.

## Contributing

If you want to expand the functionality of the API clients, or fix a bug, feel free to fork and do a pull request back onto the 'master' branch. Make sure the tests pass.
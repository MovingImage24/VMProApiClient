# VMPro PHP API Client

## Requirements

* Guzzle 5.* or 6.* - make sure you have these installed in your project already

## Installation

To install the API client, run the following command:

```
composer require movingimage/vmpro-api-client
```

## Usage

### With Guzzle6

To use the VMPro API Client with Guzzle 6, you can use the factory like this:

```
<?php

use MovingImage\Client\VMPro\ApiClientFactory;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once('./vendor/autoload.php');

$logger = new Logger('test');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));

$creds = new ApiCredentials('ruben.knol@movingimage.com', 'Example123');
$factory = new ApiClientFactory();
$client = $factory->create('https://api-qa.video-cdn.net/v1/vms/', $creds, [], $logger);

$channel = $client->getChannels(5);
echo $channel->getName();
```

### With Guzzle5

To use the VMPro API Client with Guzzle 6, you can use the factory like this:

```
<?php

use MovingImage\Client\VMPro\Factory\Guzzle5ApiClientFactory as ApiClientFactory;
use MovingImage\Client\VMPro\Entity\ApiCredentials;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once('./vendor/autoload.php');

$logger = new Logger('test');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));

$creds = new ApiCredentials('<username>', '<password>');
$factory = new ApiClientFactory();
$client = $factory->create('https://api.video-cdn.net/v1/vms/', $creds, [], $logger);

$channel = $client->getChannels(5);
echo $channel->getName();
```

## Maintainers

* Ruben Knol <ruben.knol@movingimage.com>

If you have questions, suggestions or problems, feel free to get in touch with the maintainers by e-mail.

## Contributing

If you want to expand the functionality of the API clients, or fix a bug, feel free to fork and do a pull request back onto the 'master' branch.
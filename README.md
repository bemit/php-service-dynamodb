# PHP DynamoDB Service

[![Latest Stable Version](https://poser.pugx.org/bemit/dynamodb/version.svg)](https://packagist.org/packages/bemit/dynamodb)
[![Latest Unstable Version](https://poser.pugx.org/bemit/dynamodb/v/unstable.svg)](https://packagist.org/packages/bemit/dynamodb)
[![codecov](https://codecov.io/gh/bemit/php-service-dynamodb/branch/master/graph/badge.svg?token=D80MD3SR7Q)](https://codecov.io/gh/bemit/php-service-dynamodb)
[![Total Downloads](https://poser.pugx.org/bemit/dynamodb/downloads.svg)](https://packagist.org/packages/bemit/dynamodb)
[![Github actions Build](https://github.com/bemit/php-service-dynamodb/actions/workflows/blank.yml/badge.svg)](https://github.com/bemit/php-service-dynamodb/actions)
[![PHP Version Require](http://poser.pugx.org/bemit/dynamodb/require/php)](https://packagist.org/packages/bemit/dynamodb)

PHP DynamoDB service class, with item-data converters.

```shell
composer require bemit/dynamodb
```

Usage:

```injectablephp
use Bemit\DynamoDB\DynamoService;

$service = new DynamoService(
    string $region,
    string $dynamo_key, string $dynamo_secret,
    ?string $endpoint = null,
    $debug = false,
    // optionally overwrite the converters:
    $item_to_array = null, $array_to_item = null,
);

// just the dynamodb client:
$client = $service->client();

// $arr = ['some-key' => 'the-text']
$item = $service->arrayToItem($arr);
// $arr_e = 'the-text'
$item_p = $service->parseArrayElement($arr_e);

// $item = ['some-key' => ['S' => 'the-text']]
$arr = $service->itemToArray($item);
// $item_p = ['S' => 'the-text']]
$arr_p = $service->parseItemProp($item_p);
```

Modes supported:

- `S`, strings
- `N`, numerics, cast by `(float)`, keeps numbers the same in e.g. JSON (no `.0`)
- `BOOL`, booleans
- `M`, maps, `stdClass` or assoc arrays
    - for safest usage uses `stdClass`, for app-side you should use e.g. non-assoc `json_decode`
    - supports nested maps & lists
- `L`, lists / arrays **or empty array**
    - supports nested lists & maps
- `NULL`, null values

## Dev Notices

Commands to set up and run e.g. tests:

```bash
# on windows:
docker run -it --rm -v %cd%:/app composer install --ignore-platform-reqs

docker run -it --rm -v %cd%:/var/www/html php:8.1-rc-cli-alpine sh

docker run --rm -v %cd%:/var/www/html php:8.1-rc-cli-alpine sh -c "cd /var/www/html && ./vendor/bin/phpunit --testdox -c phpunit-ci.xml"

#docker-compose run --rm test sh -c "cd /var/www/html && composer update"
docker-compose run --rm test sh

cd /var/www/html && ./vendor/bin/phpunit --coverage-text --testdox -c phpunit-ci.xml

cd /var/www/html && ./vendor/bin/phpunit --coverage-html coverage --testdox -c phpunit-ci.xml

# on unix:
docker run -it --rm -v `pwd`:/app composer install

docker run -it --rm -v `pwd`:/var/www/html php:8.1-rc-cli-alpine sh

docker run --rm -v `pwd`:/var/www/html php:8.1-rc-cli-alpine sh -c "cd /var/www/html && ./vendor/bin/phpunit --testdox -c phpunit-ci.xml"
```

## Versions

This project adheres to [semver](https://semver.org/), **until `1.0.0`** and beginning with `0.1.0`: all `0.x.0` releases are like MAJOR releases and all `0.0.x` like MINOR or PATCH, modules below `0.1.0` should be considered experimental.

## License

This project is free software distributed under the [**MIT LICENSE**](LICENSE).

> Amazon DynamoDB® is a trademark of Amazon.com, Inc. No endorsements by Amazon.com, Inc. are implied by the use of these marks.

### Contributors

By committing your code to the code repository you agree to release the code under the MIT License attached to the repository.

***

Maintained by [Michael Becker](https://mlbr.xyz)

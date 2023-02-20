# Setle API

PHP client for the [Setle](https://www.setle.be) API. For information and terms of use, refer to the
[official documentation](https://public-api.setle.app/).

## Installation

`composer require fw4/setle-api "^2.0"`

## Usage

```php
// Instantiate the API using a client id and client secret
$api = new Setle\Setle('0123456789abcdef', '0123456789abcdef');

// Request a list of estates
$estates = $api->getEstates();
```

## Access tokens

Access tokens are managed by the library, and long-running scripts will automatically trigger a refresh when a token
expires. Due to the short lifespan of Setle's access tokens it is not recommended to manage tokens manually, but it is
supported through the `requestAccessToken()` and `setAccessToken()` methods.

## License

`fw4/setle-api` is licensed under the MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

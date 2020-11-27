# Setle API

PHP client for the [Setle](https://www.setle.be) API. For information and terms of use, refer to the
[official documentation](https://api.setle.be).

## Installation

`composer require fw4/setle-api`

## Usage

```php
// Instantiate the API using a broker token
$api = new Setle\Setle('0123456789abcdef');

// Request a list of estates
$estates = $api->whise()->getEstates();
```

### Available endpoints

Use the following methods to access available endpoints:

```php
$api->whise()->getEstates();
$api->whise()->getEstate($id);
$api->skarabee()->getEstates();
$api->skarabee()->getEstate($id);
$api->sweepbright()->getEstates();
$api->sweepbright()->getEstate($id);
```

## Access tokens

Access tokens are managed by the library, and long-running scripts will automatically trigger a refresh when a token
expires. Due to the short lifespan of Setle's access tokens it is not recommended to manage tokens manually, but it is
supported through the `requestAccessToken()` and `setAccessToken()` methods.

## License

`fw4/setle-api` is licensed under the MIT License (MIT). Please see [LICENSE](LICENSE) for more information.

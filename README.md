# PHP Utilities

![Packagist Version](https://img.shields.io/packagist/v/datalinx/php-utils)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/datalinx/php-utils)
[![codecov](https://codecov.io/gh/DataLinx/php-utils/branch/master/graph/badge.svg?token=47T4ZAX33D)](https://codecov.io/gh/DataLinx/php-utils)
![Packagist License](https://img.shields.io/packagist/l/datalinx/php-utils)
![Packagist Downloads](https://img.shields.io/packagist/dt/datalinx/php-utils)
[![Test runner](https://github.com/DataLinx/php-utils/actions/workflows/run-tests.yml/badge.svg)](https://github.com/DataLinx/php-utils/actions/workflows/run-tests.yml)

## About
This package is a collection of classes that provide a fluent OOP interface to manipulate common primitive data types such as strings, numbers, arrays and other structures by providing a lean wrapper around libraries from other packages. 

See the changelog [here](CHANGELOG.md).

## Requirements
- PHP >= 7.4
- `mbstring` and `intl` PHP extensions
- `picqer/php-barcode-generator` package, if you want to use the `FluentBarcode` wrapper
- Linux server is supported

## Installing
Download it with composer:
```shell
composer require datalinx/php-utils
````

If you want to use the `FluentBarcode` wrapper (which is really cool!), install the additional dependency:
```shell
composer require picqer/php-barcode-generator
````

## Usage
With an out-of-the-box installation, you must create an instance of each utility and then interact with it.
```php
$string = new \DataLinx\PhpUtils\Fluent\FluentString('My  string');
echo $string->clean(); // Outputs: My string
```
However, if you include the `src/fluent_helpers.php` file, you can use the helper functions to create new instances in a leaner way:
```php
echo str('My  string')->clean(); // Outputs: My string
```
You can also create your own helper functions to create new instances, if the ones this library provides do not suite you or already exist in your project. **The library-provided helper functions are not loaded by default**.

## Function documentation
_Auto-generated documentation coming soon._

## Contributing
If you have some suggestions how to make this package better, please open an issue or even better, submit a pull request.

The project adheres to the PSR-4 and PSR-12 standards.

### Changelog
All notable changes to this project are automatically documented in the [CHANGELOG.md](CHANGELOG.md) file using the release workflow, based on the [release-please](https://github.com/googleapis/release-please) GitHub action.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

For all this to work, commit messages must follow the [Conventional commits](https://www.conventionalcommits.org/) specification, which is also enforced by a Git hook. 

# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.6.0] - 2023-02-23
### Added
- Support for Laravel 10.x

## [1.5.0] - 2022-02-12
### Added
- Support for Laravel 9.x

## [1.4.2] - 2020-02-13
### Added
- New method cartItemQuantitySet() to update quantity of a cart item directly.

## [1.4.1] - 2020-12-11
### Added
- Support for PHP 8

## [1.4.0] - 2020-09-12
### Added
- Support for Laravel 8.x

## [1.3.0] - 2020-03-04
### Added
- Support for Laravel 7.x

## [1.2.0] - 2019-09-04
### Added
- Support for Laravel 6.x

## [1.1.0] - 2019-07-06
### Added
- Support for Laravel 5.8
- `currency` config option for displaying the numbers.
- Replaced the config option `LC_MONETARY` with `locale` as per the updates.

### Fixed
- Replaced `money_format()` with NumberFormatter [#11](https://github.com/freshbitsweb/laravel-cart-manager/issues/11)
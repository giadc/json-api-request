# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.0.2] - 2022-11-02 

### Fixed

-   `getQueryString` now uses correct implode character. 

## [3.0.1] - 2022-11-01 

### Fixed

-   Fixed default value for `Sorting` in `RequestParams`'s `fromArray` method, `array` provided, expected `string`.

## [3.0.0] - 2022-09-12

### Changed

-   Requires PHP 8.0 or greater.
-   Updates supporting packages to meet PHP version requirements.

## [2.0.0] - 2020-11-11

### Added

-   Added new options, `fields` and `excludes`.
-   RequestParams may now be built from provided array.

### Changed

-   RequestParams now allows for the modification of fields.

## [1.2.0] - 2019-12-09

### Changed

-   Lessened restrictions on dependency versions.
-   Updated PHPUnit and Mockery dev dependencies.

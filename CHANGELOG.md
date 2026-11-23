# Changelog

## 0.1.0 (2026-11-23)

### ⚠ BREAKING CHANGES

* The ideal_postcodes.<resource> services and their IdealPostcodes\Resources aliases are removed; inject IdealPostcodes\Client instead.

### refactor

* Register only the client

### Features

* Add the Symfony bundle
* Add the PHP version to the User-Agent
* Send both keys as Authorization: Bearer

### Bug Fixes

* Name the Symfony bundle class so Flex registers it
* Keep the bundle and SDK identifiers beside a configured User-Agent

## Changelog

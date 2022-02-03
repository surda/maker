# Nette Maker helps you create empty files.
-----
[![Build Status](https://travis-ci.org/surda/maker.svg?branch=master)](https://travis-ci.org/surda/maker)
[![Licence](https://img.shields.io/packagist/l/surda/maker.svg?style=flat-square)](https://packagist.org/packages/surda/maker)
[![Latest stable](https://img.shields.io/packagist/v/surda/maker.svg?style=flat-square)](https://packagist.org/packages/surda/maker)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-brightgreen.svg?style=flat)](https://github.com/phpstan/phpstan)

## Installation

The recommended way to is via Composer:

```
composer require --dev surda/maker
```

After that you have to register extension in config.neon:

```yaml
extensions:
    maker: Surda\Maker\DI\MakerExtension
```

## Commands

```bash
./bin/console.sh make:entity
./bin/console.sh make:entity Foo\\Bar\BarEntity
```

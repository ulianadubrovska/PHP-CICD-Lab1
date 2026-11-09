# PHP CI/CD Lab 1

![PHP CI Application](https://github.com/ulianadubrovska/PHP-CICD-Lab1/actions/workflows/php-ci.yml/badge.svg)

## Description

A simple PHP calculator project created to practice
Git version control and CI/CD automation using GitHub Actions.

## Features

- Addition of two numbers
- Subtraction of two numbers
- Automated unit testing with PHPUnit
- Code style validation using Laravel Pint
- Automated CI pipeline with GitHub Actions

## Technologies

- PHP 8.2
- Composer
- PHPUnit
- Laravel Pint
- Git and GitHub
- GitHub Actions

## Running Tests

Install dependencies:

```bash
composer install
```

Run unit tests:

```bash
./vendor/bin/phpunit tests/Unit
```

Check code style:

```bash
./vendor/bin/pint --test
```

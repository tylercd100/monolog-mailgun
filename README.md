# Monolog Mailgun Handler
[![Latest Version](https://img.shields.io/github/release/tylercd100/monolog-mailgun.svg?style=flat-square)](https://github.com/tylercd100/monolog-mailgun/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/tylercd100/monolog-mailgun.svg?branch=master)](https://travis-ci.org/tylercd100/monolog-mailgun)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tylercd100/monolog-mailgun/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tylercd100/monolog-mailgun/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/tylercd100/monolog-mailgun/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tylercd100/monolog-mailgun/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/56f3252c35630e0029db0187/badge.svg?style=flat)](https://www.versioneye.com/user/projects/56f3252c35630e0029db0187)
[![Total Downloads](https://img.shields.io/packagist/dt/tylercd100/monolog-mailgun.svg?style=flat-square)](https://packagist.org/packages/tylercd100/monolog-mailgun)

A Monolog Handler for [Mailgun](http://www.mailgun.com)

## Installation

Install via [composer](https://getcomposer.org/) - In the terminal:
```bash
composer require tylercd100/monolog-mailgun
```

## Usage
```php
use Tylercd100\Monolog\Handler\MailgunHandler;

$to      = "to@test.com";
$from    = "from@test.com";
$subject = "Test Email!";
$token   = "1onfifaln234nfkdo02";
$domain  = "test.com";

$handler = new MailgunHandler($to, $subject, $from, $token, $domain);
$logger  = new Monolog\Logger('mailgun.example');
$logger->pushHandler($handler);
$logger->addCritical("Foo Bar!");
```
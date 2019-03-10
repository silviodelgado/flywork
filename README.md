<img src="https://www.interart.com/utils/logo-flywork2.png" alt="Flywork">

# Flywork
Lightweight PHP MVC framework

![version](https://img.shields.io/badge/version-2.0-green.svg?maxAge=2592000)
[![Latest Stable Version](https://poser.pugx.org/interart/flywork/v/stable)](https://packagist.org/packages/interart/flywork)
[![Latest Unstable Version](https://poser.pugx.org/interart/flywork/v/unstable)](https://packagist.org/packages/interart/flywork)
![composer.lock](https://poser.pugx.org/interart/flywork/composerlock)

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/d1ced1265dbb45b7ad5b82d072105ac9)](https://www.codacy.com/app/silviodelgado/flywork?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=silviodelgado/flywork&amp;utm_campaign=Badge_Grade)
[![Code Climate](https://codeclimate.com/github/silviodelgado/flywork/badges/gpa.svg)](https://codeclimate.com/github/silviodelgado/flywork)
[![StyleCI](https://github.styleci.io/repos/171210922/shield?branch=master)](https://github.styleci.io/repos/171210922)

<a href="https://github.com/silviodelgado/flywork/issues">
  <img src="https://img.shields.io/github/issues/silviodelgado/flywork.svg" alt="Issues">
</a>
<a href="https://github.com/silviodelgado/flywork/blob/master/LICENSE">
  <img src="https://img.shields.io/github/license/silviodelgado/flywork.svg" alt="MIT license">
</a>
<a href="https://github.com/silviodelgado/flywork/">
  <img src="https://img.shields.io/badge/forkable-yes-green.svg" alt="This is a forkable respository">
</a>


## What is this?

**Flywork** is a lightweight **PHP framework**, using **MVC** pattern, to help you to create web applications rapidly.

You can see a proof of concept project in [Flywork-poc](https://github.com/silviodelgado/flywork-poc) project.

## Main Features

**Flywork** provides you the following features:

* Route (custom and auto-route) management
* RESTful request management support
* Database access and handling
* View management (using native engine)
* Cache management
* Session management
* Language (translation) handling
* Bundle engine for JS e CSS files (with minifier)
* Form fields handling
* Mail encapsulated adapter
* Log management - PSR-3 compliant
* Security methods (password, encryption and decryption)
* Too few settings do start

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=U4XC3N7P7FV7L&item_name=Help+Flywork+development&currency_code=USD&source=url"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" alt="Donate now!"></a>

## Requirements

* PHP 7.1+
* [Composer](https://getcomposer.org/) Dependency Manager
* [Apache 2.4 Server](https://httpd.apache.org/)
  * ```mod_rewrite``` enabled

## Dependencies

* Language
  * [PDO](http://php.net/manual/pt_BR/book.pdo.php) (PHP Data Objects) Extension (for Medoo library)
  * [MbString](http://php.net/manual/en/book.mbstring.php) Extension
  * [OpenSSL](http://php.net/manual/pt_BR/book.openssl.php) Extension
* External libraries
  * [Medoo](https://medoo.in)
    * DB access/management
  * [Minify](https://www.minifier.org)
    * Minify assets (CSS and JS) files
  * [PHPMailer](https://phpmailer.github.io/PHPMailer)
    * Library for send e-mails
  * [PSR/Log](https://packagist.org/packages/psr/log)
    * Log management interface


## Get Started

### Install via composer

Add Flywork to composer.json configuration file.

```
$ composer require interart/flywork
```

Then update the composer

```
$ composer update
```

See the proof of concept project in [Flywork-poc](https://github.com/silviodelgado/flywork-poc) to get more details about using features.


## License

This project is licensed under [The MIT License (MIT)](/LICENSE).

This is a free software (like free beer), but you can consider donate to help its development.<br>
You can use [Paypal (click in this link)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=U4XC3N7P7FV7L&item_name=Help+Flywork+development&currency_code=USD&source=url) to make a donation securely.

## Contributing

If you have any idea how this project can be improved, plese see [Contributing](https://github.com/silviodelgado/flywork/blob/master/CONTRIBUTING.md) section to get details. Thank you. :)

## Roadmap

* Change Session management class to accept others engines
* Add Unit Tests

&copy;2018-2019 [Silvio Delgado](https://www.silviodelgado.net)


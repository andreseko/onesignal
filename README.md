# Onesignal for Laravel

## Introduction

This is a simple OneSignal wrapper library for Laravel. It simplifies the basic notification flow with the defined methods. You can send a message to all users or you can notify a single user. Before you start installing this service, please complete your OneSignal setup at https://onesignal.com and finish all the steps that is necessary to obtain an application id and REST API Keys.

[![Latest Stable Version](https://poser.pugx.org/andreseko/onesignal/version)](https://packagist.org/packages/andreseko/onesignal)
[Total Downloads](https://poser.pugx.org/andreseko/laravel/downloads)](https://packagist.org/packages/andreseko/laravel)
[![License](https://poser.pugx.org/andreseko/onesignal/license)](https://packagist.org/packages/andreseko/onesignal)
[![Build Status](https://travis-ci.org/andreseko/onesignal.svg?branch=master)](https://travis-ci.org/andreseko/onesignal)

## Requirements
- [Composer](https://getcomposer.org)

## Installation

First, you'll need to require the package with Composer:

```bash
composer require andreseko/onesignal
```

**You only need to do the following if your Laravel version is below 5.5**:

Then, update `config/app.php` by adding an entry for the service provider.

```php
[
    'providers' => [
        // ...
        AndreSeko\OneSignal\OneSignalServiceProvider::class
    ]
];
```

Then, register class alias by adding an entry in aliases section

```php
[
    'aliases' => [
        // ...
        'OneSignal' => AndreSeko\OneSignal\OneSignalFacade::class
    ]
];
```


Finally, from the command line again, run 

```bash
php artisan vendor:publish --tag=config
``` 

## Configuration

You need to fill in `onesignal.php` file that is found in your applications `config` directory.
`app_id` is your *OneSignal App ID* and `rest_api_key` is your *REST API Key*. Also you can override the parameters with your `.env` file.

## Usage

### Send a notification for all platforms

You can easily send a message to all registered users with the command:

```php
OneSignal::setTitle('MY APP')
    ->setSubTitle('My best app ever')
    ->setMessage('My cool message')
    ->sendNotification();
```

### Send a notification for iOS only

Sending a message to iOS users:

```php
OneSignal::setTitle('MY APP')
    ->setSubTitle('My best app ever')
    ->setMessage('My cool message')
    ->setPlataform(OneSignal::IOS)
    ->setPlataform(OneSignal::ANDROID, false)
    ->setPlataform(OneSignal::WEB, false)
    ->configureIos('my_custom_sound.caf')
    ->sendNotification();
```

### Send a notification for Android only

Sending a message to Android users:

```php
OneSignal::setTitle('MY APP')
    ->setSubTitle('My best app ever')
    ->setMessage('My cool message')
    ->setPlataform(OneSignal::ANDROID)
    ->setPlataform(OneSignal::IOS, false)
    ->setPlataform(OneSignal::WEB, false)
    ->configureAndroid('my_custom_sound')
    ->sendNotification();
```

### Get all notifications

Getting all notifications from Onesignal

```php
$response = OneSignal::getNotifications();
```

Please refer to https://documentation.onesignal.com/reference for all customizable parameters.
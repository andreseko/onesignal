# Onesignal

## Introduction

This is a simple OneSignal wrapper library for Laravel. It simplifies the basic notification flow with the defined methods. You can send a message to all users or you can notify a single user. Before you start installing this service, please complete your OneSignal setup at https://onesignal.com and finish all the steps that is necessary to obtain an application id and REST API Keys.

## Requirements
- [Composer](https://getcomposer.org)

## Installation

First, you'll need to require the package with Composer:

```
$ composer require andreseko/onesignal
```

## Configuration

In your .env file, create the follow variables:



You need to fill in `onesignal.php` file that is found in your applications `config` directory.
`app_id` is your *OneSignal App ID* and `rest_api_key` is your *REST API Key*.

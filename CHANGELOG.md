# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.6] - 2019-12-08
### Fixed
- ScheduleFor method signature to accepts Illuminate\Support\Carbon and Carbon\Carbon

## [1.0.5] - 2019-12-08
### Added
- Support a Carbon Datetime library
- Method to set filters
### Changed
- Method schedule to scheduleFor with support a Carbon Datetime.

## [1.0.4] - 2019-12-05
### Fixed
- Publish config file by service provider

## [1.0.3] - 2019-12-05
### Fixed
- Laravel minimum version

## [1.0.2] - 2019-12-05
### Changed
- Upgrade the requirements to php 7
- Fixed the Laravel usage to 5.8

## [1.0.1] - 2019-11-30
### Fixed
- Fixed the config path

## [1.0.1] - 2019-11-29
### Added
- Send push notifications for all platforms
- Send push notifications for specific segments
- Send push notifications for individual users
- Send push notifications for iOS only
- Send push notifications for Android only
- Send push notifications for Web only
- Push with specific content [see official documentation](https://documentation.onesignal.com/reference#create-notification)
- Get the all push notifications
- Get the push notifications specific sent by dashboard, api or automated
- Get individual push notification
- Delete a push notification
- Get all players
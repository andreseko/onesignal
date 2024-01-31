# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2024-01-30
### Added
- Update to use Laravel version to 10.0

## [1.6.0] - 2022-08-07
### Fixed
- Fixed composer dependencies

## [1.5.1] - 2022-06-12
### Fixed
- Fixed composer dependencies

## [1.5.0] - 2022-06-04
### Added
- Upgrade Laravel version to 9.0
- PHP minimum required 7.4

## [1.4.1] - 2022-06-12
### Fixed
- Fixed composer dependencies

## [1.4.0] - 2021-08-12
### Added
- Upgrade Laravel version to 8.0

## [1.3.0] - 2020-03-29
### Added
- Upgrade Laravel version to 7.0

## [1.2.0] - 2020-01-15

### Added
- Location filter by method setLocation with lat, long and radius in meters.

## [1.1.0] - 2019-12-09

### Added
- Interface and better documentation

### Changed
- Method name from scheduleFor to scheduleTo

## [1.0.6] - 2019-12-08

### Fixed
- ScheduleFor method signature to accepts Illuminate\Support\Carbon and Carbon\Carbon

## [1.0.5] - 2019-12-08

### Added
- Support a Carbon Datetime library
- Method to set filters

### Changed
- Method schedule to scheduleFor with support a Carbon Datetime.

### Deprecated
- Method schedule will be removed in release 1.1.0

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

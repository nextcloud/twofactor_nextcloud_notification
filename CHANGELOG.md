# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.2] - 2019-01-07
### Changed
- Use the public interface for background jobs
- Updated translations
- Set supported databases (sqlite, mysql/mariadb, postgres)

### Fixed
- Should be compatible with IE now

## [1.0.1] - 2018-12-07
### Fixed
- Use provided user so the provider can always load

## [1.0.0] - 2018-12-07
### Added
- Nextcloud 16 support

### Changed
- App is now part of the 2FA settings like a 1st class citizen
- New and updated translations

### Removed
- Nextcloud 14 support

## [0.2.4] - 2018-10-23
### Added
- Added Notification Manager [#28](https://github.com/nickv-nextcloud/twofactor_nextcloud_notification/pull/28)
- Feedback when the login is approved on waiting screen [#29](https://github.com/nickv-nextcloud/twofactor_nextcloud_notification/issues/29)

## [0.2.3] - 2018-10-21
### Fixed
- Allow to disable provider properly as well [#27](https://github.com/nickv-nextcloud/twofactor_nextcloud_notification/pull/27)

## [0.2.2] - 2018-10-19
### Added
- Screenshots added [#15](https://github.com/nickv-nextcloud/twofactor_nextcloud_notification/issues/15)
- Prepare for translations [#20](https://github.com/nickv-nextcloud/twofactor_nextcloud_notification/issues/20)
- Added CHANGELOG.md

### Fixed
- Notifications expired attempts are cleaned up [#8](https://github.com/nickv-nextcloud/twofactor_nextcloud_notification/issues/8)

## [0.2.1] - 2018-10-18
### Fixed
- Made info.xml compliant

## [0.2.0] - 2018-10-18
### Added
- Basic provider implementation

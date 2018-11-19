# Changelog

All Notable changes to `mapudo/guzzle-bundle` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [unreleased] - 2018-11-19
### Changed
- Add `duration` property to be logged in `LogMiddleware` to keep track of the request duration

## [2.1.1] - 2018-01-11
### Changed 
- Changed the template include paths

## [2.1.0] - 2017-12-13
### Changed
- Changed isset handling with default value to php 7
- Added Symfony 4 support

## [2.0.0] - 2017-09-28
### Changed
- **[BC break]** - Annotated `auth` in client `request_options` can also be a string, due to [guzzle/oauth-subscriber](https://github.com/guzzle/oauth-subscriber)
- When registering middleware `method` is not required any more, as middleware can work with `__invoke` (e.g. [guzzle/oauth-subscriber](https://github.com/guzzle/oauth-subscriber))

## [1.1.0] - 2017-03-10
### Changed
- Requests that are logged with the LogMiddleware, will now be logged with a client name
- Symfony Profiler Handler now allows filtering by client
- Symfony Profiler Handler now shows the client as a label on the request
- Symfony profiler now looks even better in Symfony3

## 2017-03-03 

### Added
- Initial commit

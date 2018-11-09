# Change Log

## [2.0.0](https://github.com/schmittjoh/metadata/tree/2.0.0) (2018-11-09)

No changes from **2.0.0-RC1**

## [1.7.0](https://github.com/schmittjoh/metadata/tree/1.7.0) (2018-10-26)
**Merged pull requests:**

- Allow Read-only Cache [\#74](https://github.com/schmittjoh/metadata/pull/74) ([goetas](https://github.com/goetas))

## [2.0.0-RC1](https://github.com/schmittjoh/metadata/tree/2.0.0-RC1) (2018-10-17)
**Merged pull requests:**

- Moved to psr-4 [\#73](https://github.com/schmittjoh/metadata/pull/73) ([samnela](https://github.com/samnela))

## [2.0.0-beta1](https://github.com/schmittjoh/metadata/tree/2.0.0-beta1) (2018-09-12)
**Closed issues:**

- Read-Only Filesystem Support [\#71](https://github.com/schmittjoh/metadata/issues/71)
- Change license to MIT [\#68](https://github.com/schmittjoh/metadata/issues/68)
- Composer.lock is out of date [\#55](https://github.com/schmittjoh/metadata/issues/55)
- consider changing chmod to @chmod [\#50](https://github.com/schmittjoh/metadata/issues/50)
- Big performance hit when upgrading from 1.4.2 to 1.5.0 [\#44](https://github.com/schmittjoh/metadata/issues/44)
- metadata name not present leads to exception [\#39](https://github.com/schmittjoh/metadata/issues/39)

**Merged pull requests:**

- Allow Read-only Cache [\#72](https://github.com/schmittjoh/metadata/pull/72) ([pdugas](https://github.com/pdugas))
- Code style  [\#70](https://github.com/schmittjoh/metadata/pull/70) ([goetas](https://github.com/goetas))
- Change license to MIT [\#69](https://github.com/schmittjoh/metadata/pull/69) ([goetas](https://github.com/goetas))
- simplified class metadata  [\#67](https://github.com/schmittjoh/metadata/pull/67) ([goetas](https://github.com/goetas))
- Fix an exception message [\#65](https://github.com/schmittjoh/metadata/pull/65) ([hason](https://github.com/hason))
- Actualized version constant. [\#64](https://github.com/schmittjoh/metadata/pull/64) ([Aliance](https://github.com/Aliance))

## [1.6.0](https://github.com/schmittjoh/metadata/tree/1.6.0) (2016-12-05)
**Closed issues:**

- Consider switching to the MIT/BSD license or a dual license otherwise [\#58](https://github.com/schmittjoh/metadata/issues/58)
- Unexpected return value [\#52](https://github.com/schmittjoh/metadata/issues/52)
- Why 0666 mode for cache file [\#48](https://github.com/schmittjoh/metadata/issues/48)
- Tons of I/O operations caused by NullMetadata [\#45](https://github.com/schmittjoh/metadata/issues/45)

**Merged pull requests:**

- Add PsrCacheAdapter [\#63](https://github.com/schmittjoh/metadata/pull/63) ([nicolas-grekas](https://github.com/nicolas-grekas))
- 50 suspress chmod warning [\#53](https://github.com/schmittjoh/metadata/pull/53) ([gusdecool](https://github.com/gusdecool))
- Adaption for complying with SPDX identifiers [\#51](https://github.com/schmittjoh/metadata/pull/51) ([valioDOTch](https://github.com/valioDOTch))

## [1.5.1](https://github.com/schmittjoh/metadata/tree/1.5.1) (2014-07-12)
**Merged pull requests:**

- Added more PHP versions and HHVM [\#47](https://github.com/schmittjoh/metadata/pull/47) ([Nyholm](https://github.com/Nyholm))
- Fix NullMetadata performance issue [\#46](https://github.com/schmittjoh/metadata/pull/46) ([adrienbrault](https://github.com/adrienbrault))
- Fixed logic bug. [\#41](https://github.com/schmittjoh/metadata/pull/41) ([flip111](https://github.com/flip111))
- Update FileCache.php added fallback option when rename fails on windows [\#40](https://github.com/schmittjoh/metadata/pull/40) ([flip111](https://github.com/flip111))

## [1.5.0](https://github.com/schmittjoh/metadata/tree/1.5.0) (2013-11-05)
**Closed issues:**

- Branch alias [\#38](https://github.com/schmittjoh/metadata/issues/38)

**Merged pull requests:**

- Don't make MetadataFactory final [\#37](https://github.com/schmittjoh/metadata/pull/37) ([bakura10](https://github.com/bakura10))
- Cache when there is no metadata for a class [\#36](https://github.com/schmittjoh/metadata/pull/36) ([adrienbrault](https://github.com/adrienbrault))
- Allow to add drivers to a driver chain [\#35](https://github.com/schmittjoh/metadata/pull/35) ([bakura10](https://github.com/bakura10))

## [1.4.2](https://github.com/schmittjoh/metadata/tree/1.4.2) (2013-09-13)
**Closed issues:**

- Update changelog [\#33](https://github.com/schmittjoh/metadata/issues/33)
- Error in Symfony2's production environment \(only\) caused with version \>= 1.4.0 [\#32](https://github.com/schmittjoh/metadata/issues/32)

**Merged pull requests:**

- Set cache files to be world readable [\#34](https://github.com/schmittjoh/metadata/pull/34) ([tommygnr](https://github.com/tommygnr))

## [1.4.1](https://github.com/schmittjoh/metadata/tree/1.4.1) (2013-08-27)
## [1.4.0](https://github.com/schmittjoh/metadata/tree/1.4.0) (2013-08-25)
## [1.3.0](https://github.com/schmittjoh/metadata/tree/1.3.0) (2013-01-22)
**Closed issues:**

- Ability to eager-load possible metadata [\#19](https://github.com/schmittjoh/metadata/issues/19)

**Merged pull requests:**

- misc cleanup [\#23](https://github.com/schmittjoh/metadata/pull/23) ([vicb](https://github.com/vicb))
- \[Cache\] Remove a race condition [\#22](https://github.com/schmittjoh/metadata/pull/22) ([vicb](https://github.com/vicb))
- Added configs for ci services [\#21](https://github.com/schmittjoh/metadata/pull/21) ([j](https://github.com/j))
- Advanced metadata implementation. [\#20](https://github.com/schmittjoh/metadata/pull/20) ([j](https://github.com/j))
- Remove incorrect docblocks [\#18](https://github.com/schmittjoh/metadata/pull/18) ([adrienbrault](https://github.com/adrienbrault))

## [1.2.0-RC](https://github.com/schmittjoh/metadata/tree/1.2.0-RC) (2012-08-21)
**Closed issues:**

- install version 1.0.0 with composer [\#9](https://github.com/schmittjoh/metadata/issues/9)
- create version/tag 1.1.1 [\#3](https://github.com/schmittjoh/metadata/issues/3)

**Merged pull requests:**

- Added the branch alias and changed the constraint on common [\#8](https://github.com/schmittjoh/metadata/pull/8) ([stof](https://github.com/stof))
- Add trait test [\#6](https://github.com/schmittjoh/metadata/pull/6) ([Seldaek](https://github.com/Seldaek))
- Fix locating files for classes without namespace [\#5](https://github.com/schmittjoh/metadata/pull/5) ([Seldaek](https://github.com/Seldaek))
- Add ApcCache [\#4](https://github.com/schmittjoh/metadata/pull/4) ([henrikbjorn](https://github.com/henrikbjorn))

## [1.1.1](https://github.com/schmittjoh/metadata/tree/1.1.1) (2012-01-02)
**Closed issues:**

- More documentation requested [\#1](https://github.com/schmittjoh/metadata/issues/1)

**Merged pull requests:**

- Add composer.json [\#2](https://github.com/schmittjoh/metadata/pull/2) ([Seldaek](https://github.com/Seldaek))

## [1.1.0](https://github.com/schmittjoh/metadata/tree/1.1.0) (2011-10-04)
## [1.0.0](https://github.com/schmittjoh/metadata/tree/1.0.0) (2011-07-09)


\* *This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)*

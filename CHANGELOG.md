# Changelog

## [v1.4.0](https://github.com/Naoray/blueprint-nova-addon/releases/tag/v1.4.0) (2020-09-21)
**Added**
- support for Laravel 8

## [v1.3.3](https://github.com/Naoray/blueprint-nova-addon/releases/tag/v1.3.3) (2020-08-03)
**Fixed**
- compatibility with [blueprint release 1.15.3](https://github.com/laravel-shift/blueprint/releases/tag/v1.15.3) (https://github.com/Naoray/blueprint-nova-addon/commit/1bf468c588beab99b1c385ef9426873630cf117a, https://github.com/Naoray/blueprint-nova-addon/commit/d3f3cd79766034604e4279340d25d814d9b87f07)
  
## [v1.3.1](https://github.com/Naoray/blueprint-nova-addon/releases/tag/v1.3.1) (2020-07-09)
**Changed**
- locked dependency version of `laravel-shift/blueprint` to `^1.15.0` (https://github.com/Naoray/blueprint-nova-addon/commit/b03f57028333477acef7c8a2bdbfc96661c86601)

**Fixed**
- wrong README example (https://github.com/Naoray/blueprint-nova-addon/commit/537ab3b891e5947020a41dbac5f48eef73db75ea)
- make package compatible with version `larvel-shift/blueprint:^1.15.0 https://github.com/laravel-shift/blueprint/pull/276 (https://github.com/Naoray/blueprint-nova-addon/commit/da2bf40cbb61af5b33d219b6254e6fa1d66631a3)
  
## [v1.3.0](https://github.com/Naoray/blueprint-nova-addon/releases/tag/v1.3.0) (2020-06-05)
**Added**
- Support for Laravel 6 #14 (thanks to @jyrkidn)

## [v1.2.0](https://github.com/naoray/blueprint-nova-addon/tree/v1.2.0) (2020-05-07)
**Added**
- `nova_blueprint` config [929feb6f](https://github.com/Naoray/blueprint-nova-addon/commit/929feb6f6dd8330c3b5037971ccda3b3f19daede)
- `timestamps` config which enables turning off timestamp fields creation for all resources [929feb6f](https://github.com/Naoray/blueprint-nova-addon/commit/929feb6f6dd8330c3b5037971ccda3b3f19daede#diff-ef12ae52b8e7cab1ee3384afcdb1f607R109)

**Changed**
- The service provider is now deferrable [887d3f4a](https://github.com/Naoray/blueprint-nova-addon/commit/887d3f4a1b41db79ae75a9c019e87e72ba950ebb)
- Tasks which add fields to the nova resource are now bound to the `NovaGenerator` class [929feb6f6](https://github.com/Naoray/blueprint-nova-addon/commit/929feb6f6dd8330c3b5037971ccda3b3f19daede#diff-ce67c1a17e4b8189d80af961be48ba22R37)

## [v1.1.1](https://github.com/naoray/blueprint-nova-addon/tree/v1.1.1) (2020-05-05)
**Fixed**

- duplicate `json` Field rules [c9050271](https://github.com/Naoray/blueprint-nova-addon/commit/c90502715848d960efc1687b93bc92409678416f)

**Changed**

- `getNovaNamespace()` is now `protected` instead of `private` (https://github.com/Naoray/blueprint-nova-addon/pull/9)

## [v1.1.0](https://github.com/naoray/blueprint-nova-addon/tree/v1.1.0) (2020-04-19)

[Full Changelog](https://github.com/naoray/blueprint-nova-addon/compare/v1.0.0...v1.1.0)

**Implemented enhancements:**

- Add nullable to BelongsTo relations when column is nullable [\#4](https://github.com/Naoray/blueprint-nova-addon/issues/4)

## [v1.0.0](https://github.com/naoray/blueprint-nova-addon/tree/v1.0.0) (2020-04-17)

[Full Changelog](https://github.com/naoray/blueprint-nova-addon/compare/1ae951c7cdaa821bbc4486c915361fe9ee63605b...v1.0.0)

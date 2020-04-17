# Blueprint Nova Addon

[![Build Status](https://img.shields.io/travis/naoray/blueprint-nova-addon/master.svg?style=flat-square)](https://travis-ci.org/naoray/blueprint-nova-addon)
[![Total Downloads](https://img.shields.io/packagist/dt/naoray/blueprint-nova-addon.svg?style=flat-square)](https://packagist.org/packages/naoray/blueprint-nova-addon)

:mega: Shoutout to [Jason McCreary](https://github.com/jasonmccreary) whose [Blueprint](https://github.com/laravel-shift/blueprint) package lays the groundwork for this small addon. Thank you Jason :raised_hands:

Installing this addon will allow you to generate your Nova resources with the `php artisan blueprint:build` command.

## Installation
You can install the package via composer:

```bash
composer require naoray/blueprint-nova-addon --dev
```

> :warning: You need to have [laravel nova](nova.laravel.com/) installed in order for the resource generation to take place!

## Usage
Refer to [Blueprint's Basic Usage](https://github.com/laravel-shift/blueprint#basic-usage) to get started. Afterwards you can run the `blueprint:build` command to generate Nova resources automatically. To get an idea of how easy it is you can use the example `draft.yaml` file below.

```yaml
# draft.yaml
models:
  Post:
    author_id: id:user
    title: string:400
    content: longtext
    published_at: nullable timestamp
    relationships:
      HasMany: Comment

  Comment:
    post_id:i 
    content: longtext
    published_at: nullable timestamp
```

From these simple 20 lines of YAML, Blueprint will generate all of the following Laravel components:

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email krishan.koenig@gmail.com instead of using the issue tracker.

## Credits

- [Krishan König](https://github.com/naoray)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).

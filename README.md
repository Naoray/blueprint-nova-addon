# Blueprint Nova Addon

![Build Status](https://travis-ci.org/Naoray/blueprint-nova-addon.svg?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/naoray/blueprint-nova-addon.svg?style=flat)](https://packagist.org/packages/naoray/blueprint-nova-addon)

:mega: Shoutout to [Jason McCreary](https://github.com/jasonmccreary) whose [Blueprint](https://github.com/laravel-shift/blueprint) package lays the groundwork for this small addon. Thank you Jason :raised_hands:

Installing this addon will allow you to generate your Nova resources with the `php artisan blueprint:build` command.

## Installation
You can install this package and **Blueprint** via composer:

```bash
composer require --dev naoray/blueprint-nova-addon
```

> :warning: You need to have [laravel nova](https://nova.laravel.com/) installed in order for the resource generation to take place!

## Usage
Refer to [Blueprint's Basic Usage](https://github.com/laravel-shift/blueprint#basic-usage) to get started. Afterwards you can run the `blueprint:build` command to generate Nova resources automatically. To get an idea of how easy it is you can use the example `draft.yaml` file below.

```yaml
# draft.yaml
models:
  Post:
    author_id: id foreign:users
    title: string:400
    content: longtext
    published_at: nullable timestamp
    relationships:
      HasMany: Comment

  Comment:
    post_id: id foreign
    content: longtext
    published_at: nullable timestamp
```

From these 13 lines of YAML, this addon will generate 2 Nova resources which are pre-filled with 14 fields.

```php
// App/Nova/Comment.php
public function fields(Request $request)
{
    return [
        ID::make()->sortable(),

        Textarea::make('Content')
            ->rules('required', 'string'),

        DateTime::make('Published at'),

        BelongsTo::make('Post'),

        DateTime::make('Created at'),
        DateTime::make('Updated at'),
    ];
}

// App/Nova/Post.php
public function fields(Request $request)
{
    return [
        ID::make()->sortable(),

        Text::make('Title')
            ->rules('required', 'string', 'max:400'),

        Textarea::make('Content')
            ->rules('required', 'string'),

        DateTime::make('Published at'),

        BelongsTo::make('Author', 'author', User::class),

        HasMany::make('Comments'),

        DateTime::make('Created at'),
        DateTime::make('Updated at'),
    ];
}
```

## Configuration
You may publish the configuration with the following command:

```bash
php artisan vendor:publish --tag=nova_blueprint
```

### Timestamp fields
To disable the generation of `timestamp` fields for all Nova resources set this option to `false`.

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email krishan.koenig@gmail.com instead of using the issue tracker.

## Credits

- [Krishan König](https://github.com/naoray)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

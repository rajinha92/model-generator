# Model Generator Laravel

This package generates models and migrations (including FK migrations)


## Service Provider

```php
\Rafael\ModelGenerator\GeneratorServiceProvider::class,
```

## artisan command

```shell
php artisan generate:model --migration --namespace=Models
```

the two options are optional, when not provided --migration then migrations will not be generated, --namespace will always be under app folder and your App\ namespace (or whatever you called it), when omitted models will be generated in app folder

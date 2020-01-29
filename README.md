# Laravel Deployment

With this package you can easily deploy your laravel projects <br>
[Latest Version on Packagist][link-packagist]

## Installation

Via Composer

```bash
composer require rmh-media/laravel-deployment
```

If you do not run Laravel 5.5 (or higher), then add the service provider in `config/app.php`:

```php
RmhMedia\LaravelDeployment\DeploymentServiceProvider::class,

```

Create the deployments table

```bash
php artisan vendor:publish --tag=migrations
php artisan migrate
```

Add path to composer autoload config

```json
"autoload": {
    "classmap": ["database/deployments"]
}
```

## Available commands

**Command:**

```bash
php artisan deploy:list --undone
```

**Result:**
The command outputs a list of executed deployments  
__--undone:__ Show only not executed deployments

**Command:**

```bash
php artisan make:deployment <version> --command=<list of commands>

# e.g.
php artisan make:deployment v1.2.1 --command="migrate --force" --command="routes:list"
```

**Result:**
Create a new deployment file

**Command:**

```bash
php artisan deploy:exec --all --done --force
```

**Result:**
The command executes maintenance tasks after successful code deployment

__--all:__ Execute all available deployments  
__--done:__ Mark all available deployments as done  
__--force:__ Force execution of already ran deployment

## Credits

- [rmh-media][link-author]

## License

Please see [license.md](license.md) for more information.

[link-packagist]: https://packagist.org/packages/rmh-media/laravel-deployment
[link-downloads]: https://packagist.org/packages/rmh-media/laravel-deployment
[link-author]: https://github.com/rmh-media


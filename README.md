# Laravel Deployment
With this package you can easily deploy your laravel projects <br>
[Latest Version on Packagist][link-packagist]

## Installation

Via Composer
```bash
$ composer require rmh-media/laravel-deployment --dev
```
If you do not run Laravel 5.5 (or higher), then add the service provider in `config/app.php`:
```php
RmhMedia\LaravelDeployment\DeploymentServiceProvider::class,
```
If you do run the package on Laravel 5.5+, [package auto-discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518) takes care of the magic of adding the service provider.
Be aware that the auto-discovery also means that this package is loaded in your production environment. Therefore you may [disable auto-discovery](https://laravel.com/docs/5.5/packages#package-discovery) and instead put in your `AppServiceProvider` something like this:
```php
if ($this->app->environment('local')) {
    $this->app->register('RmhMedia\LaravelDeployment\DeploymentServiceProvider');
}
```
## Available commands
**Command:**
```bash
$ php artisan deploy:list
```
**Result:**
The command outputs a list of exectued deployments

**Command:**
```bash
$ php artisan make:deployment
```
**Result:**
Create a new deployment file

**Command:**
```bash
$ php artisan deploy:sanitize
```
**Result:**
The command executes maintenance and sanitizing tasks after successful code deployment

## Credits

- [rmh-media][link-author]

## License
The EU Public License. Please see [license.md](license.md) for more information.

[link-packagist]: https://packagist.org/packages/rmh-media/laravel-deployment
[link-downloads]: https://packagist.org/packages/rmh-media/laravel-deployment
[link-author]: https://github.com/rmh-media


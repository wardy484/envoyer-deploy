# Envoyer Deploy

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tutorauk/envoyer-deploy.svg?style=flat-square)](https://packagist.org/packages/tutorauk/envoyer-deploy)

Envoyer deployer is a laravel package that enables you to deploy to your  Laravel Envoyer site from an artisan command!

```shell
php artisan envoyer:deploy
```


## Installation

You can install the package via composer:

```bash
composer require tutorauk/envoyer-deploy
```

You will need an envoyer api token. You can generate one on the [Envoyer site](https://envoyer.io/user/api-tokens). You should currently only need the `deployments:create` and `deployments:delete` scope.

Add this to your .env file:
````dotenv
ENVOYER_API_TOKEN=<your token>
````

You should next publish the config file:
```shell
php artisan vendor:publish --tag="envoyer-deploy-config"
```

Then change the default_project and default_branch to suite your project. 

In order to ensure that the branch selection isn't an endless list of branches that exist on your machine but don't really exist anymore, it is highly recommended you purge some of the dead branches from your machine.
```
git fetch -p
``` 

## Usage

Artisan deploy is a tool that allows you to easily deploy to the staging from the command line.

You can trigger a deployment of remote branches using the following command:
```shell
php artisan deploy
```
This will give you a prompt to select a branch. By default this will be pre-populated with your current branch (regardless of whether it exists remote).

Should you wish to quickly deploy to main you can pre-populate the prompt with the `-m` or `--main` flag:
```shell
php artisan deploy -m
```

You can optionally skip the branch prompt using `-f`. This will deploy your current branch or master if used in combination with `-m`:
```shell
php artisan deploy -f
```

You can list the most recent deployments using:
```shell
php artisan deploy:list
```

You can cancel the most recent deployment with:
```shell
php artisan deploy:cancel
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

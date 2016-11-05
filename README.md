# CarcelUserBundle - Development application

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/f5b8027a-eb3b-422e-98a2-c138d2ceb426/mini.png)](https://insight.sensiolabs.com/projects/f5b8027a-eb3b-422e-98a2-c138d2ceb426)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/damien-carcel/user-bundle-dev/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/damien-carcel/user-bundle-dev/?branch=master)
[![Build Status](https://travis-ci.org/damien-carcel/user-bundle-dev.svg?branch=master)](https://travis-ci.org/damien-carcel/user-bundle-dev)

This repository is a Symfony full stack application containing the CarcelUserBundle in its src folder. This application is used only for development purposes.

The bundle itself is available as a read-only [subtree split repository](https://github.com/damien-carcel/UserBundle) and on [packagist](https://packagist.org/packages/carcel/user-bundle).

## About the bundle

CarcelUserBundle is a ready to use user management system for Symfony 3 applications, using Doctrine ORM for database storage.

It provides all the elements you need out of the box, yet let you the possibility to customize it at your liking.

This bundle is build upon the [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle).

## Contributing

There is still much to do for this bundle. Please have a look at the [list of issues](https://github.com/damien-carcel/user-bundle-dev/issues).
If you are interested by an issue, don't hesitate to leave a comment to take contact.

If you encountered a bug or miss a functionality, don't hesitate to raise an issue, but please make sure there is not already a similar one.

## Requirements

- PHP 5.6+
- PHP Modules:
    - apcu
    - curl
    - gd
    - intl
    - mysql
    - mcrypt
- MySQL 5.1 and above or MariaDB

## Installation

### Download and install from GitHub

If you didn't, start by [installing composer](https://getcomposer.org/download/). You also need to prepare a MySQL database.

Just clone the repository, and install dependencies by running `php composer.phar update`. Composer will ask you for your application configuration (database name, user and password).

Then you can populate this database with a basic set of [doctrine fixtures](https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html) provided by the bundle (or create your own, of course):

```bash
php bin/console doctrine:schema:update --force
php bin/console doctrine:fixtures:load
```

### Deploy the assets

Run the following command:

```bash
bin/console assets:install
bower update
```

### Run the application

The application is now ready to run. You can either configure a HTTP server of your choice (Apache, nginx…) or use the Symfony internal server:

```bash
php bin/console server:run
```

You should now be able to display the login page (your.application.domain.name/login).

## License

This repository is under the MIT license. See the complete license in the `LICENSE` file.

a Laravel 4 Package to generate Template Data Definition classes from existing database tables

## Installation

Begin by installing this package through Composer. Edit your project's `composer.json` file to require `sule/tdd`.

    "require": {
        "laravel/framework": "4.0.*",
        "sule/tdd": "dev-master"
    },
    "minimum-stability" : "dev"

Next, update Composer from the Terminal:

    composer update

Once this operation completes, the final step is to add the service provider. Open `app/config/app.php`, and add a new item to the providers array.

    'Sule\Tdd\TddServiceProvider'

That's it! You're all set to go. Run the `artisan` command from the Terminal to see the new `generate` command.

    php artisan

## Usage

Use following command to check all available options.

    php artisan generate:tdd --help

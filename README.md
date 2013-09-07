a Laravel 4 Package to generate Template Data Definition classes from existing database tables. The generated classes will be very useful in your html view. This will make you to stop assigning a lot of variables to your view via controller.

You will just need to get one or many data from your database table via model, instantiate and assign the model to the template class that having the same name with your table / model, then assign the template class to the view.

This still need some work to do, to make the template data definition easy to use.

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

All classes will be created under '/app/templates' folder by default, so make sure you had the required folder, but you can specify the folder path as you wish.

Use following command to check all available options.

    php artisan generate:tdd --help

## Work to do

Detect related table, so we can use the related table data directly without have to assign them in the controller.

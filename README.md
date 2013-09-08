a Laravel 4 Package to generate Template Data Definition classes from existing database tables. The generated classes will be very useful in your html view. This will make you to stop assigning a lot of variables to your view via controller.

You will just need to get one or many data from your database table via model, instantiate and assign the model to the template class that having the same name with your table / model, then assign the template class to the view.

Each generated classes will also having 'interface' class generated.

The nice thing about it, if you need to format some of data before displayed in the view, you can do that easly in the template class method directly, and all of the view using it will be having the same format. No need to edit all controller or class that already make such formating.

This still need some work to do, to make the template data definition easy to use.

## How to use the generated class

In example you are going to use template class for "user" table, then find the generated template class that having the same name with the table (in default having "Template" suffix).

Open the template class, then you will see below line of codes:

    use User; // This the "user" table model class name

    public function setData(User $data)

So make sure you are having the same class name, or just modify the above line of codes regarding your existing model class name.

Now get a row data from your "user" table using your model, and assign the template class to the view

    $user = User::find(1); // 1 is the id of the user data row

    $userTemplate = new UserTemplate;
    $userTemplate->setData($user);

    View::make('your_view_name', array(
        'user' => $user
    ));

Let see how we going to use it in the view (I'm using Twig template engine in below example)

    <div>User ID : {{ user.id }}</div>
    <div>User Email : {{ user.email }}</div>

You can also create process in template class method for on-demand formating and just pass the argument via the view

    <div><img src="{{ user.photo('small') }}" alt="" /></div>

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

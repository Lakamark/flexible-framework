# Installation 

To run the demo. You need to lunch some command line and to edit the configuration.


Run composer install to generate the vendor directory.

````bash
composer install
````

## Use an env file instead of config.php!
In the old version, you should create a `config.php` file in the root proj to set up your environment.
To respect the standard, I decided to change the configuration system in the framework. 
To edit the configuration, you should to rename file `.example.env` to `.env` or to `env.local` in the root project.
Then change the database keys to adapt the framework to your dev environment. 

It is safe and flexible with this new way. 

````bash
## This file is all your environment.
# We commanded to follow this pattern.
#  * .env                contains default values for the environment variables needed by the app
#  * .env.local          uncommitted file with local overrides.
#
# It is commander to publish .example.env.
# On your local environment to use this template to create your own .env file.

# Global FlexibleFramework env variables
APP_ENV=prod

## Database connection
DATABASE_HOST='!changeMe!'
DATABASE_USERNAME='!changeMe!'
DATABASE_PASSWORD='!changeMe!'
DATABASE_NAME='!changeMe'

## ...
````

To create your database on mysql server

````bash
CREATE DATABASE demo;
````

Now you have a database.
You can run the migration via phinx command line.
[To learn more about phinx on the official 
documentation.](https://book.cakephp.org/phinx/0/en/index.html)

````bash
vendor/bin/phinx migrat
````

Once you are created the table schema.
You can run the seeding command to generate some fake data.

````bash
vendor/bin/phinx seed:run
````

When you have finished configuring the project. You can run the PHP server.
Be careful!
You should define the **public** directory. It is the main entry to run the application.

````bash
php -S  localhost:8000 -t public
````   

That's it!
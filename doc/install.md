## Installation 

To run the demo. You need to lunch some command line and to edit the configuration.


Run composer install to generate the vandor directory.

````bash
composer install
````

In the root project. Duplicate the file config.dist.php to config.php. Ensure to  
````php
<?php

/**
 * The main configuration for your database.
 */
return [
    'database.host' => 'localhost',
    'database.username' => 'root',
    'database.password' => 'root',
    'database.name' => 'flexible',
];
````

To create your database on mysql server

````bash
CREATE DATABASE demo;
````

Now you have a database. You can run the migration via phinx command line

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
# Requirements

See https://github.com/roccosportal/pvik/wiki/Requirements .
A database is also required (MySQL prefered).


# For Developers

First clone the project to your webserver folder e.g. `/var/www`.

`git clone git@github.com:roccosportal/dashbird.git`

Change into new directory.

`cd dashbird/`

Initializing submodules.

`git submodule init`

Get the submodule projects.

`git submodule update`


Create a database named `dashbird` on your Database Server (MySQL prefered).

Execute `bin/structure.sql` in your new database.

Change the access data in `Application/Configs/Config.php`

```php5
<?php
self::$Config['MySQL']['Server'] = 'localhost';
self::$Config['MySQL']['Username'] = 'root';
self::$Config['MySQL']['Password'] = 'root';
self::$Config['MySQL']['Database'] = 'dashbird';
```


Javascript and CSS Files are merged together (you need to run this after every version change in the Config.php).

Change into the project `bin` directory.

`cd bin/`

Execute make files.

`php makejs.php`

`php makecss.php`
A CLI Git tool

WIP

## Dev Environment Notes:
Need to create `src/stan.php` for PHPStan to find constants, this file should look like this:
```php
<?php
define('ROOT', getcwd());
define('APP_ROOT', __DIR__);
```
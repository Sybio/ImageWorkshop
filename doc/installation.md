# ImageWorkshop

## Installation

The class is designed for **PHP version 5.4+**, it includes new specifications like namespaces. If you use an **older version**, it can work until you remove these specifications. See how to install and use the class depending of your PHP version:

### PHP with an autoloader

The PHPImageWorkshop can be installed with [Composer](https://getcomposer.org/). Run this command:

```sh
composer require sybio/image-workshop
```

The PHPImageWorkshop folder added in your lib directory, just use the class namespace in your main script and that's it:

```php
// myscript.php:
use PHPImageWorkshop\ImageWorkshop;

$layer = ImageWorkshop::initXXX(...);
```

### PHP without autoloader

If you don't use an autoloader, this is a little more tricky but still easy to install: you just need to include files manually.

#### Include dependencies

You have to **load** classes at the beginning of each **class files** that are dependant, with `include()` or `require_once()` functions.

To do that, just **uncomment //require_once()** call at the beginning of each class file, an example for ImageWorkshop.php file:

```php
// ImageWorkshop.php, line 9:

// If no autoloader, uncomment these lines:
require_once(__DIR__.'/Core/ImageWorkshopLayer.php');
require_once(__DIR__.'/Exception/ImageWorkshopException.php');
```

Don't forget the other classes: Core/ImageWorkshopLayer.php, Core/ImageWorkshopLib.php, etc...

If you don't do that, an error like that will be displayed:

```
Fatal error: Class 'PHPImageWorkshop\ImageWorkshopException' not found in /home/.../ImageWorkshop.php on line 1731
```

#### Include ImageWorkshop in your main script

Where you need to use the class don't forget to include it !

```php
// myscript.php:
require_once('path/to/lib/PHPImageWorkshop/ImageWorkshop.php'); // Be sure of the path to the class
```

#### Usage

You can now initialize an object like this:

```php
// myscript.php:
$layer = new PHPImageWorkshop\ImageWorkshop::initXXX(...);
```

Alternatively, a better thing is to use the namespace:

```php
// myscript.php:

use PHPImageWorkshop\ImageWorkshop; // Use the namespace of ImageWorkshop

require_once('path/to/lib/PHPImageWorkshop/ImageWorkshop.php'); // Be sure of the path to the class
```

By this way, you can initialize an object simplier:

```php
// myscript.php:
// previous code ...

$layer = ImageWorkshop::initXXX(...);
```

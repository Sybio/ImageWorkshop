# ================================
# ImageWorkshop class
# ================================

![Test status](https://secure.travis-ci.org/Sybio/ImageWorkshop.png)

### Summary and features
Really flexible and easy-to-use PHP class to work with images using the GD Library

http://phpimageworkshop.com/

### Latest updates

**Version 2.0.0 - 2012-11-21**

New version of ImageWorkshop ! The library is now divided in 3 main classes for cleaned code:
- ImageWorkshopLayer: the class which represents a layer, that you manipulate
- ImageWorkshop: a factory that is used to generate layers
- ImageWorkshopLib: a class containing some tools (for calculations, etc...), used by both classes

Technically, only the initialization change compared with the 1.3.x versions, check the documentation:
http://phpimageworkshop.com/documentation.html#chapter-initialization-of-a-layer

Here an example, before and now:
```php
    // before
    $layer = new ImageWorkshop(array(
        'imageFromPath' => '/path/to/images/picture.jpg',
    ));
```

```php
    // now
    $layer = ImageWorkshop::initFromPath('/path/to/images/picture.jpg');
```

And also the installation of the class: http://phpimageworkshop.com/installation.html

The documentation has been updated, you can now check the documentation of each version since 1.3.3:
(Ex: http://phpimageworkshop.com/doc/9/initialize-from-an-image-file.html?version=2.0.0, http://phpimageworkshop.com/doc/9/initialize-from-an-image-file.html?version=1.3.3)

**Version 1.3.4 - 2012-11-20**
- Renaming "Pourcent" occurrences for "Percent"
For example, the method resizeInPourcent() is now named resizeInPercent().
Check the changes: https://github.com/Sybio/ImageWorkshop/pull/9/files

**Version 1.3.3 - 2012-10-25**
- Adding an ImageWorkshopException class in the project to manage exceptions

**Version 1.3.1 - 2012-10-17**
- Fixing a transparency bug when saving a layer as PNG which has no sublayer.

**Version 1.3.0 - 2012-10-11**
- You are able to apply a horizontal or vertical flip (transformation) on a layer:

```php
$layer->flip('horizontal');
```
- Refactoring mergeTwoImages() method.

### Installation

The class is designed for PHP 5.3+, but it can work with older PHP versions... Check how to install the class here: http://phpimageworkshop.com/installation.html

### Usage

- Learn how to use the class in 5 minutes: http://phpimageworkshop.com/quickstart.html
- The complete documentation: http://phpimageworkshop.com/documentation.html
- Usefull tutorials: http://phpimageworkshop.com/tutorials.html

**What's new in the doc' ?**

- Installation guide: http://phpimageworkshop.com/installation.html
- Adding the flip documentation: http://phpimageworkshop.com/doc/25/flip-vertical-horizontal-mirror.html
- Adding the opacity documentation which was omitted: http://phpimageworkshop.com/doc/24/opacity-transparency.html
- Tutorial "Manage animated GIF with ImageWorkshop (and GiFFrameExtractor & GifCreator)": http://phpimageworkshop.com/tutorial/5/manage-animated-gif-with-imageworkshop.html

### @todo
- Adding a method to add easily borders to a layer (external, inside and middle border)
- Check given hexa' color and remove # if exists.

### Contributors
Main contributors:
- Clément Guillemain - Freelance [Sybio / @Sybio01]
          
And also:
- Cédric Spalvieri - Novaway [skwi69]
- Elton Minetto - Coderockr [eminetto]
- Phil Sturgeon - HappyNinjas Ltd. [philsturgeon]
- [ziadoz]
- Frank de Jonge - Ku [FrenkyNet]
- Bjørn Børresen - Freelancer [bjornbjorn]
- Turneliusz - [Turneliusz]
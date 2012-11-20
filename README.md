# ================================
# ImageWorkshop class
# ================================

![Test status](https://secure.travis-ci.org/Sybio/ImageWorkshop.png)

### Summary and features
Really flexible and easy-to-use PHP class to work with images using the GD Library

http://phpimageworkshop.com/

### Latest updates

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

**Version 1.2.6 - 2012-09-27**
- You can now initialize a layer from an image string (obtained with cURL, file_get_contents...):
```php
    $imgString = file_get_contents("/myfolder/pic.jpg");

    $layer = new ImageWorkshop(array(
        "imageFromString" => $imgString,
    ));
```
Be carefull, JPEG format is known to be badly encoded after a cURL request or file_get_contents()
and can show display bugs ! I'm trying to find a solution.

**Version 1.2.5 - 2012-09-21**
- You can now find tests status of the class on travis-ci: http://travis-ci.org/#!/Sybio/ImageWorkshop
- Adding ImageWorkshop on http://travis-ci.org/ for controlled continuous integration

**Version 1.2.5 - 2012-09-18**
- Fixing the only one known bug that we are tracking for a long time: you can know
apply a rotation (->rotate()) and then an opacity (->opacity()) on a layer without crash.
- Updating composer.json to autoload the class in a project using composer
- Testing if the PHP environment has GD library enabled

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
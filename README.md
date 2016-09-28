# ================================
# ImageWorkshop class
# ================================

[![Test status](https://secure.travis-ci.org/Sybio/ImageWorkshop.png)](https://travis-ci.org/Sybio/ImageWorkshop)

### Summary and features
Really flexible and easy-to-use PHP class to work with images using the GD Library

http://phpimageworkshop.com/

### Latest updates

**Version 2.0.9 - 2015-07-05**

- Fix ImageWorkshop::initFromPath with remote URL

**Version 2.0.8 - 2015-06-01**

- Fix exception code when file not found

**Version 2.0.7 - 2015-03-22**

- Allow `ImageWorkshop::initFromPath` factory working with remote URL
- Improve PHP >= 5.5 compatibility
- Add `fixOrientation` method to layer to change image orientation based on EXIF orientation data
- Fix background color when value is setting to "000000"

**Version 2.0.6 - 2014-08-01**

@jasny (https://github.com/jasny) contribution, new methods :

* `ImageWorkshopLayer::resizeToFit()` resizes an image to fit a bounding box.
* `ImageWorkshopLayer::cropToAspectRatio()` crops either to width or height of the document to match the aspect ratio.

Documentation here : https://github.com/Sybio/ImageWorkshop/pull/37#issue-28704248

**Version 2.0.5 - 2013-11-12**

- Implementing interlace mode (http://php.net/manual/en/function.imageinterlace.php) on save() method to display progessive JPEG image

```php
    $interlace = true; // set true to enable interlace, false by default
    $layer->save($dirPath, $filename, $createFolders, $backgroundColor, $imageQuality, $interlace);
```

Thanks @dripolles (https://github.com/dripolles) & @johnhunt (https://github.com/johnhunt)

**Version 2.0.4 - 2013-09-11**

- Fix a major bug when resizing both sides AND conserving proportion : layer stack problem (current layer has a new 
nested level in its stack, not expected), and translations with positionX and positionY are wrong.
Fixed.
(Initial problem : https://github.com/Sybio/ImageWorkshop/pull/14)
- Add a parameter to clearStack() method

**Version 2.0.2 - 2013-06-14**

- Fix a new bug : when resizing or cropping, small images can have 0 pixel of width or height (because of round), which
is impossible and script crashes. Now width and height are 1 pixel minimum.

Note: 

```php
$layer->resizeInPixel(null, 0 /* or negative number */, null);
```

It will generate a 1 pixel height image, not 0.

**Version 2.0.1 - 2013-06-03**

- Fix an opacity bug : pure black color (#000000) always displayed fully transparent (from 0 to 99% opacity). Bug fixed ! (no known bug anymore)
- Add some Exceptions to help debugging

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

# ImageWorkshop

## Layer

ImageWorkshop could be used as a layer.

### 1. You said layer ?

A layer is a rectangle (or a square) which has a width and a height, and a background image having the same width and height. You can resize, crop, rotate, etc, a layer: its width and height and also its background image will be affected. This is how you rework your images.

### 2. How to use

#### Initialization:

By default, you can declare a virgin layer, which will have a transparent background like this:

```php
$layer = ImageWorkshop::initVirginLayer(300, 200); // width: 300px, height: 200px
````

But this case is usefull only if you want to create and work on a new virgin image. In practice, you probably will create a layer from an existing image file:

```php
$layer = ImageWorkshop::initFromPath('/path/to/images/picture.jpg');
````

By this way, your layer will automatically have the width and height of the image "picture.jpg", and its background will be this picture !

There are many ways to initialize a layer, just read the [chapter "Layer"](../documentation.md#2-initialization-of-a-layer).

#### Performing an action:

You can perform an action on the layer, like resizing:

```php
$thumbWidth = 125;
$thumbHeight = 125;

$layer->resizeInPixel($thumbWidth, $thumbHeight);
````

The layer will be resized (and so its background image) to have a width and a height of 125pixels.

Read [chapter "Document"](../documentation.md#1-layer-notion) to discover all actions that you can perform on !

#### Dimensions:

At everytime, you can access to the width and the height of a layer, usefull to know the new dimensions after a resize for example...:

```php
$layer->getWidth(); // in pixel
$layer->getHeight(); // in pixel
````

#### Narrow / largest side:

You can also get in pixel the narrow side or the largest side of your layer:

```php
$layer->getNarrowSideWidth(); // in pixel
$layer->getLargestSideWidth(); // in pixel
````

#### Saving or showing a background image:

To show or to save the layer image, read the [chapter "Group"](../documentation.md#1-layer-notion).

The power of a layer is not limited to that, you will learn in the next page how to use a layer as a group to superpose many layers (usefull to watermark a picture...).

[<< Introduction](introduction.md) - [Group >>](group.md)

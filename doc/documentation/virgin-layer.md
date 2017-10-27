# ImageWorkshop

## Initialize a new virgin layer

You can initialize a new layer like this:

```php
$width = 300;
$height = 200;
$backgroundColor = 'FF0000'; // optionnal, can be null to transparent

$layer = ImageWorkshop::initVirginLayer($width, $height, $backgroundColor);
```

By default, the layer will have a transparent image. "backgroundColor" key is optionnal and allows you to choose the background color of your layer.

[<< Merging layers](merging-layers.md) - [From an image file >>](from-image-file.md)

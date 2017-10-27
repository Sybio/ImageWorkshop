# ImageWorkshop

## Opacity

Change the opacity / transparency of a layer and its sublayers:

```php
$newOpacity = 50;

$layer->opacity($newOpacity);
```

What we get:

![opacity result](img/xopacity-1.jpg)

### Known issues:

1. This method loops each pixel of the image to apply the transparency. Be careful, it can be extremely long with large images, and memory-consuming !
2. Always give a background color when getting the result if you don't want a transparent PNG... Otherwise, the transparency won't be saved !

Example if you want a JPEG or a GIF:

```php
$result = $layer->getResult('ffffff'); // Giving a white background
```

[<< Flip](flip.md) - [Resizing >>](resizing.md)

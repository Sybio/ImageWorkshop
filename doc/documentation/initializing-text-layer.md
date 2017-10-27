# ImageWorkshop

## Initializing a text layer

Initialize a text layer:

```php
$text = "I am the text",
$fontPath = "/path/to/fonts/arial.ttf",
$fontSize = 12,
$fontColor = "FFFFFF",
$textRotation = 0,
$backgroundColor = "FF0000", // optionnal

$layer = ImageWorkshop::initTextLayer($text, $fontPath, $fontSize, $fontColor, $textRotation, $backgroundColor);
```

GD will generate a text image which will be the layer background. The width and height are managed so.

`$backgroundColor` is optionnal, if null, the background will be transparent.

`$fontPath` must be a path to a font file (Mandatory for GD library), the advantage is that you can use an unconventional font !

[<< Uploaded image file from a form](uploaded-image.md) - [Getting the image variable >>](getting-image-variable.md)

# ImageWorkshop

## Writing

### 1. Initialize a text layer

The best way to add some text is to use a layer with a text on its background, see the page [Initializing a text layer](initializing-text-layer.md).

Then, [superimpose](surperimposition-levels.md) your text layer on your main layer !

### 2. Pasting a text (not recommended)

You can also paste directly a text on the background of a layer:

```php
$text = "Here my text";
$font = 1; // Internal font number (http://php.net/manual/fr/function.imagestring.php)
$color = "ffffff";
$positionX = 0;
$positionY = 0;
$align = "horizontal";

$layer->writeText($text, $font, $color, $positionX, $positionY, $align);
```

[<< Rotating](rotating.md) - [Cloning an ImageWorkshop object >>](cloning-imageworkshop-object.md)

# ImageWorkshop

## Getting the image variable

Call the `getResult()` method to get a variable containing the merged image of your document:

```php
$backgroundColor = "FFFFFF";

$image = $layer->getResult($backgroundColor);
```

Note that it doesn't merge physically your sublayers, and you can continue to work after calling the method.

`$backgroundColor` can be set null or "transparent" if you want to get a transparent PNG image.

Be careful, always give a none transparent background color if you want to get a JPG or GIF image: otherwise, the transparency of sublayers won't be conserved and you'll probably have some display bugs !

You have the image variable, now you will see how to show it in the navigator.

[<< Initializing a text layer](initializing-text-layer.md) - [Showing >>](showing.md)

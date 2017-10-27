# ImageWorkshop

## Cloning an ImageWorkshop object

When you want to copy an existant ImageWorkshop object, always use the native php [clone method](http://php.net/manual/en/language.oop5.cloning.php) !

```php
$layer2 = clone $layer1;
```

Here the bad way, never do it:

```php
$layer2 = $layer1;
```

Never forget to clone if you want to get a sublayer too !

```php
$layer2 = clone $layer1->getLayer(3);
```

Why ?

A php image variable stores a reference to an image in the memory, and when you copy an existant variable, this reference doesn't change: so you've got 2 variables with the same reference to the same image, if you modify the image of one of this variables, the other variable which references on the same image will unfortunetaly point the modified image...
The clone method manages this: it copies and creates a new image on the background of layers.

When you add a layer in the stack of another one, the cloning is automatically managed by the `addLayer()` method.

[<< Writing](writing.md) - [List of methods >>](list-of-methods.md)

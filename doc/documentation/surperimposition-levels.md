# ImageWorkshop

## Surperimposition & levels

We see previously that an ImageWorkshop object has a stack of sublayers, empty by default. You can use a layer as a group by adding sublayers in its stack.

### 1. Adding a layer in a group stack

You can add a sublayer in a group like this:

```php
$sublayerInfos = null;

$level = 2; // Level 2 in the stack of $group
$sublayer = $layer;
$positionX = 80; // Left position in px
$positionY = 40; // top position in px
$position = "LB";

$sublayerInfos = $group->addLayer($level, $sublayer, $positionX, $positionY, $position);
```

`$level`: you choose the level of the sublayer in the stack. For example, level 2 is on the top of level 1, level 3 on the top of level 2, etc...

If you choose a too high level, the sublayer will be placed at the highest level possible (for example, if you add a sublayer at level 3 in the stack, but there is no sublayer at level 2, this sublayer will be replaced at level 2 and not 3).

Also, if you add a sublayer at a level already taken by another sublayer, it will be added at this level, but the old sublayer and all highest placed sublayers will be shift of one level (*For example, if you have `$sublayer1` at level 1 in the stack, `$sublayer2` at level 2, `$sublayer3` at level 3 and you add `$sublayer4` at level 1, `$sublayer1` will be shift at level 2, `$sublayer2` at level 3 and `$sublayer3` at level 4*).

`$position` is the position where to place the sublayer background on the group background, see the [Corners / positions schema of an image](corners-schema-image.md) to choose a position. The default `$position` is "LT" (from left top).

`$positionX` and `$positionY` represent translations in pixel from `$position` to place the sublayer background.

If you are programming a dynamic script using ImageWorkshop, you probably will know the id of the sublayer in the stack of the group (to work on it after), and also its correct level (if you choose a too high level), after added it.
Fortunately, `addLayer()` method return an array containing these informations:

```php
echo $sublayerInfos['id']; // Id of the sublayer in the group stack
echo $sublayerInfos['layerLevel']; // final level of the sublayer
```

You probably just want to add a sublayer on the top of the stack, without knowing the current highest level, just use addLayerOnTop method and it will perform this for you:

```php
$sublayerInfos = $group->addLayerOnTop($sublayer, $positionX, $positionY, $position);
```

Know that there is method to add a sublayer at the lowest level (which is equivalently to choose level 1...):

```php
$sublayerInfos = $group->addLayerBelow($sublayer, $positionX, $positionY, $position);
```

### 2. Moving a sublayer in the stack

Imagine you want to move a sublayer at another level in the stack. Just use this:

```php
$sublayerId = 3;

$group->moveUp($sublayerId); // move the sublayer at level +1
$group->moveDown($sublayerId); // move the sublayer at level -1
```

Note that if you want to move up the sublayer at the highest level, or to move down the sublayer at level 1, it will do nothing.

Also note that the sublayer and the other one at the upper (or lower) level will exchange their level !

You can also move a sublayer directly at the highest or the lowest level thanks to `moveTop()` or `moveBottom()`:

```php
$sublayerId = 3;

$group->moveTop($sublayerId); // move the sublayer at the highest level
$group->moveBottom($sublayerId); // move the sublayer at level 1
```

Be carefull, other layers will be replaced in response.

And finally, you can move a sublayer to the needed level:

```php
$sublayerId = 3;
$level = 2;
$insertUnderTargetedLayer = true;

$group->moveTo($sublayerId, $level, $insertUnderTargetedLayer); // Move sublayer 3 to level 2
```

`$insertUnderTargetedLayer`: insert the layer under the sublayer at the given level or on its top.

If the position is too high, it will be placed at the highest position possible, other layers will be replaced in response too.

### 3. Removing a layer from a group stack

You can delete a sublayer in a group stack like this:

```php
$sublayerId = 3;

$group->remove($sublayerId); // Remove sublayer 3
```

Be carefull, other layers will be replaced in response.

You know how to place a layer in the stack of another one, now you will see how to edit a sublayer in a layer stack.

[<< Document](document.md) - [Access to a sublayer >>](access-sublayer.md)

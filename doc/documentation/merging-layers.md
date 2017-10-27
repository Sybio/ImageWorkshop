# ImageWorkshop

## Merging layers

You can merge plural sublayers in the stack of a group. Layers would be merged in one layer, and all layer backgrounds would be paste on the resulting layer:

### 1. Merge down

You can apply `mergeDown()` method to merge a sublayer having the id `$sublayerId` at level x+1 with the sublayer at level x in a group stack:

```php
$sublayerId = 2;

$group->mergeDown($sublayerId);
```

The method return an array containing the new layer id in the stack and and its new level. If the sublayer has no layer below its level in the stack, it will be merged with the parent layer background.

### 2. Merge all

You can also merge all the sublayers of a group: their backgrounds are then pasted on the group background, and its stack is cleared.

```php
$group->mergeAll();
```

You know how to add, to move and to place layers. Now, we will see different ways to initialize a layer.

[<< Access to a sublayer](access-sublayer.md) - [New virgin layer >>](virgin-layer.md)

# ImageWorkshop

## Access to a sublayer in the stack

When you add a sublayer in the stack of a group layer, you're able to continue to modify it:

```php
$sublayerId = 2;

$group->layers[$sublayerId]->doSomething();
```

Sublayers of sublayers can be modified too:

```php
$sublayer1Id = 2;
$sublayer2Id = 1;
$sublayer3Id = 4;

$group->layers[$sublayer1Id]->layers[$sublayer2Id]->layers[$sublayer3Id]->doSomething();
```

Now, we will see how to merge plural sublayers in a group stack.

[<< Surperimposition & levels](surperimposition-levels.md) - [Merging layers >>](merging-layers.md)

# ImageWorkshop

## Group

ImageWorkshop could be used as a group of layer.

### 1. What is a group

All layers can be used as a group: in fact, each ImageWorkshop object has a **stack of layers**: by default, a layer doesn't have any layer in its stack, but you can add one or plural layers (or even subgroups !) in the stack of this layer.

So, a group is a layer, which has a background image and its own width and height, but which has also one or plural layers in its stack.

The added layer at the highest level in the group stack is fully visible, and the added layer at the lowest level (level 1) can be partially (or totally) hidden by other layers in the group stack.

(An added layer at level 2 is on the top of the layer at level 1, etc...)

The background image of a group is always under the stack (level 0), so if a layer is added in the stack of the group, it will hide partially (or totally) the background image of the group.

When you have finished to use your group, you can generate a merged image of all sublayer backgrounds in the group stack: layer backgrounds will be pasted on the group background.

(The layer background at level 3 will be pasted on the layer background at level 2 and so on...)

### 2. How to use

A group is initialized like a layer: this is a layer !
You can declare it with a virgin background image, from an existing image, etc...

```php
$group = ImageWorkshop::initVirginLayer(300, 200); // width: 300px, height: 200px
```

All works exactly like a layer, but if you performed an action like a resize (for example), all the sublayers in its stack will be adjusted and resized in response !

#### Group stack

You will learn how to work with the group stack at the pages ["Surperimposition & levels"](surperimposition-levels.md), ["Access to a sublayer"](access-sublayer.md) and ["Merging layers"](merging-layers.md).

One of your group will necessarily contain all layers and groups. So it can be considered as a document, like we will see.

[<< Layer](layer.md) - [Document >>](document.md)

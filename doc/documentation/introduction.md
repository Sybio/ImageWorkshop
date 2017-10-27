# ImageWorkshop

## Introduction

### 1. Layer notion

ImageWorkshop is thought to work like a picture editing software (Photoshop, GIMP...): you arrange some layers in a document. Each layer contain a background image and are ranged in the document stack.

When you save your document, all layer backgrounds are merged in one to get the final image: the layer at level 1 is hidden by the layer at level 2, etc...

So the layer at the highest level is fully visible, and the layer at the lowest level (level 1) can be partially (or totally) hidden by other layers.

When you apply an action on your document, all layers are repositionned and arranged in response.

It make this class the most flexible ever !

### 2. ImageWorkshop VS Photoshop (or GIMP...)

With an image editing software, you place layers in the document stack. You can also place layer groups.

With ImageWorkshop, there is a biggest difference: an object can do all of this. It really simplifies the class.

### 3. ImageWorkshopLayer object

An ImageWorkshopLayer object could be 2 different things depending on how you want to use it:

* **a layer**: this is a rectangle which has a transparent background image by default and where you can paste images (from your hard drive or an upload form...) on its background.
* **a group layer**: a layer that includes multiple sublayers at different level in its stack, all leveled on the top of its background. If you perform an action on the group, all its sublayers (and subgroups) will be affected !

[Layer >>](layer.md)

# ImageWorkshop

## Creating thumbnails

### The purpose of the tutorial

There are many ways to generate a thumbnail thanks to [resize](../documentation/resizing.md) and [crop](../documentation/cropping.md) methods...
We will see 3 cases particularly that could be used.

We suppose in these methods that we want to generate thumbnails with fixed width and height, which is usually the case.

### 1. Conserving all the image

In this case, we want to conserve all the image, and not to crop a part of it.

We have to used the [4th possibility of use](../documentation/resizing.md#4th-possibility-resize-with-proportion-and-based-on-both-sides-since-12-version) of `resizeInPixel()` (or Percent) method:

```php
// Resize to get the thumbnail
$layer->resizeInPixel(200, 120, true, 0, 0, 'MM');
```

We force the image to be resized in a box of 200px / 120px.

Here an example with a picture of 640px / 480px:

![Original](img/thumbnails/xoriginal.jpg)

And the result:

![Result 1](img/thumbnails/xresult1.jpg)

I chose black background in this example to fill the box (but you can choose a transparent fill if your image is saved as a PNG, or another colour...).

Let's see with a picture having a largest height than the width (640px / 748px):

![Original 2](img/thumbnails/xoriginal2.jpg)

And the result:

![Result 2](img/thumbnails/xresult2.jpg)

Images are totally conserved, and we get thumbnails having the expected width and height.

### 2. Get squared thumbnails not in a box

What if we want to generate squared thumbnails ?

We can use the previous method, but for a matter of aesthetics, we don't want to have to place our images in black, or transparent (etc...) boxes... like this:

![Result 3](img/thumbnails/xresult3.jpg)

So we will have to use a cropping method. In a first time, we will get a square of the maximum possible of the image:

```php
// Get a square ("MM" to say in the middle of the layer) of the maximum possible
$layer->cropMaximumInPixel(0, 0, "MM");
```

If we save the layer at this step, here what we get (a squared image):

![Result 4](img/thumbnails/xresult4.jpg)

And now we will use a resize method to get a thumbnail of 200px / 200px:

```php
// Resize the squared layer in smaller sizes
$layer->resizeInPixel(200, 200);
```

The result:

![Result 5](img/thumbnails/xresult5.jpg)

All the code of this method:

```php
$layer->cropMaximumInPixel(0, 0, "MM");
$layer->resizeInPixel(200, 200);
```

### 3. Get a portrait (or landscape) format not in a box:

Finally, we decide to have thumbnails with conserved proportions not placed in a box... and not squared: this is the last case.

To perform this, we have to generate a squared layer (previous case) having the largest side of the expected thumbnail, and then to crop it. By doing this, we are sure to conserve proportion (no flattening) and to don't have to fill new edges.

```php
$expectedWidth = 200;
$expectedHeight = 120;

// Determine the largest expected side automatically
($expectedWidth > $expectedHeight) ? $largestSide = $expectedWidth : $largestSide = $expectedHeight;

// Get a squared layer
$layer->cropMaximumInPixel(0, 0, "MM");

// Resize the squared layer with the largest side of the expected thumb
$layer->resizeInPixel($largestSide, $largestSide);

// Crop the layer to get the expected dimensions
$layer->cropInPixel($expectedWidth, $expectedHeight, 0, 0, 'MM');
````

Here the result:

![Result 6](img/thumbnails/xresult6.jpg)

You can now choose your favorite method to generate thumbnails !

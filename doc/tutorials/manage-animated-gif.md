# ImageWorkshop

## Manage animated GIF with ImageWorkshop

### The purpose of the tutorial

We will see how to work with animated GIF without losing the animation because of GD library. In this tutorial, we want to add a **watermark and resize the animated GIF**.

![Original animated GIF with incredible Jim Carrey](img/gif/original.gif)

![The edited animated GIF](img/gif/edited.gif)

### GD & the GIF format

GD library is not able to work with animated GIF without breaking them. In fact, a GIF is a compression of multiple images, and GD just use the first of them !

### How to proceed so ?

This is simple: we will extract all the images (frames) of the GIF, then we will manage them (resizing, watermarking...), and finally we will re-generate the initial GIF with the edited frames !

### Prerequisite

To perform this, we will use 2 libraries I made specially for that:

* **GifFrameExtractor**: It will extract all the frames of the animated GIF, download it or learn more here: [https://github.com/Sybio/GifFrameExtractor](https://github.com/Sybio/GifFrameExtractor)
* **GifCreator**: It helps to generate an animated GIF (can be used to create new GIF). Download it or learn more here: [https://github.com/Sybio/GifCreator](https://github.com/Sybio/GifCreator)

### 1. Test the image format

To avoid bugs, we first have to check if the image is an animated GIF:

```php
$gifPath = '/folder/images/pic.gif'; // Your animated GIF path

if (GifFrameExtractor::isAnimatedGif($gifPath)) { // check this is an animated GIF

    // All the following code will be placed here...
}
```

### 2. Extract the frames

```php
// Extractions of the GIF frames and their durations
$gfe = new GifFrameExtractor();
$frames = $gfe->extract($gifPath);
```

All the frames are now contained in the $frames array.

### 3. Initializing our watermark layer

```php
// Initialization of the watermark layer
$watermarkLayer = ImageWorkshop::initFromPath('/folder/images/watermark.jpg');
```

### 4. Resize and watermark all the frames

```php
$retouchedFrames = array();

// For each frame, we add a watermark and we resize it
foreach ($frames as $frame) {

    // Initialization of the frame as a layer
    $frameLayer = ImageWorkshop::initFromResourceVar($frame['image']);

    $frameLayer->resizeInPixel(350, null, true); // Resizing
    $frameLayer->addLayerOnTop($watermark, 20, 20, 'LB'); // Watermarking

    $retouchedFrames[] = $frameLayer->getResult();
}
```

All of the edited frames are now in the $retouchedFrames array ! Now it's time to create a new GIF with them...

### 5. Creation of the GIF

```php
// Then we re-generate the GIF
$gc = new GifCreator();
$gc->create($retouchedFrames, $gfe->getFrameDurations(), 0);
```

`GifCreator::create()` expect in first param an array of images, in second parameter all the frame durations (extract previously) and the last is to choose the loop number of your GIF (give 0 for infinite loop).

### 6. Saving the result

```php
// And now save it !
file_put_contents('/folder/images/newgif.gif', $gc->getGif());
```

GifCreator generates the string source code of your GIF, you can save the string in a new file thanks to `file_put_contents()` !

### All the code of the tutorial

```php
$gifPath = '/folder/images/pic.gif'; // Your animated GIF path

if (GifFrameExtractor::isAnimatedGif($gifPath)) { // check this is an animated GIF

    // Extractions of the GIF frames and their durations
    $gfe = new GifFrameExtractor();
    $frames = $gfe->extract($gifPath);

    // Initialization of the watermark layer
    $watermarkLayer = ImageWorkshop::initFromPath('/folder/images/watermark.jpg');

    $retouchedFrames = array();

    // For each frame, we add a watermark and we resize it
    foreach ($frames as $frame) {

        // Initialization of the frame as a layer
        $frameLayer = ImageWorkshop::initFromResourceVar($frame['image']);

        $frameLayer->resizeInPixel(350, null, true); // Resizing
        $frameLayer->addLayerOnTop($watermark, 20, 20, 'LB'); // Watermarking

        $retouchedFrames[] = $frameLayer->getResult();
    }

    // Then we re-generate the GIF
    $gc = new GifCreator();
    $gc->create($retouchedFrames, $gfe->getFrameDurations(), 0);

    // And now save it !
    file_put_contents('/folder/images/newgif.gif', $gc->getGif());
}
```

Feel free to crop, resize, change the opacity, etc, of your animated GIF ;)

### Known issues

Sometimes, the edited GIF can be outputted with bad colors or even lines... Try to give a background color (or change it) with getResult('ffffff') method. This is because tranparent backgrounds can display some bugs (I'm working on ImageWorkshop to fix it).

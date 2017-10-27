# ImageWorkshop

## Showing

Call the `getResult()` method to get a variable containing the merged image of your document (previous page):

```php
$image = $layer->getResult();
```

Then, use the classical php functions to show an image:

### 1. Showing a PNG

```php
header('Content-type: image/png');
header('Content-Disposition: filename="butterfly.png"');
imagepng($image, null, 8); // We choose to show a PNG (quality of 8 on a scale of 0 to 9)
exit;
```

### 2. Showing a JPEG

```php
header('Content-type: image/jpeg');
header('Content-Disposition: filename="butterfly.jpg"');
imagejpeg($image, null, 95); // We choose to show a JPEG (quality of 95%)
exit;
```

### 3. Showing a GIF

```php
header('Content-type: image/gif');
header('Content-Disposition: filename="butterfly.gif"');
imagegif($image); // We choose to show a GIF
exit;
```

Don't forget to give a background color when you want to show a JPEG or GIF when calling `getResult()` (see the previous page):

```php
$image = $layer->getResult("ffffff"); // white background
```

You can also save an image with native php functions `imagexxx()` but ImageWorkshop provides a magic `save()` function.

[<< Getting the image variable](getting-image-variable.md) - [Saving >>](saving.md)

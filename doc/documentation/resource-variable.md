# ImageWorkshop

## Initialize with a resource variable ($image)

You can initialize a layer by giving an image variable:

```php
$image = imagecreate(200, 500);

$layer = ImageWorkshop::initFromResourceVar($image);
```

The layer will automatically get the width and the height of the image referenced by the variable. Image will be copied in the layer background.

[<< From an image file](from-image-file.md) - [Uploaded image file from a form >>](uploaded-image.md)

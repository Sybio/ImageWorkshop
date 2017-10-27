# ImageWorkshop

## Initialize with an uploaded image file from a form

 You can initialize a layer by giving an uploaded image file: it consists to initialize a layer from the temporary path of the file.

Considering that the file input of your form has a name attribute called "image":

```php
$layer = ImageWorkshop::initFromPath($_FILES['image']['tmp_name']);
```

If you are using a **framework**, this is similar: find how to get the tmp_name of your uploaded file, and if you can't, first save the file and then open it as a layer from its saved path.

Be sure to give an image file.

[<< Resource variable ($image)](resource-variable.md) - [Initializing a text layer >>](initializing-text-layer.md)

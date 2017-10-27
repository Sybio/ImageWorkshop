# ImageWorkshop

## Saving

Saving an image:

```php
$dirPath = __DIR__."/../../../../../../web/uploads/2012";
$filename = "card.png";
$createFolders = true;
$backgroundColor = null; // transparent, only for PNG (otherwise it will be white if set null)
$imageQuality = 95; // useless for GIF, usefull for PNG and JPEG (0 to 100%)

$layer->save($dirPath, $filename, $createFolders, $backgroundColor, $imageQuality);
```

By calling the save method, a merged image will be saved in a specified folder. This action generate a merged image, but doesn't merge physically the sublayers of your document: after saving, you'll be able to continue to use your document and to perform some actions on its sublayers, really convenient !

In addition, the script will check the extension of your filename and determine the mime type of the image to save.

If set true, the `$createFolders` option will create the folders for you if they don't exist. However, be sure that one of the folder in the `$dirPath` exists and has all writing permissions (chmod 777 -R).
If a file already exists, it will be override.

Give in percent the compression of a PNG image and not in a scale of 0 to 9 (conversation is managed).

If you choose a transparent background color but you want to save a JPEG or a GIF, it will automatically be white ("#ffffff"): image can't have a transparent background.

You now know how to handle layers and save or display a result, you will now see how to perform an action on a layer (and its sublayers).

[<< Showing](showing.md) - [Cropping >>](cropping.md)

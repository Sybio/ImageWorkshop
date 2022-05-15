<?php

namespace PHPImageWorkshop;

use GdImage;
use PHPImageWorkshop\Core\ImageWorkshopLayer;
use PHPImageWorkshop\Core\ImageWorkshopLib;
use PHPImageWorkshop\Exception\ImageWorkshopException;

/**
 * ImageWorkshop class
 *
 * Use this class as a factory to initialize ImageWorkshop layers
 *
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 */
class ImageWorkshop
{
    /**
     * @var int
     */
    public const ERROR_NOT_AN_IMAGE_FILE = 1;

    /**
     * @var int
     */
    public const ERROR_IMAGE_NOT_FOUND = 2;

    /**
     * @var int
     */
    public const ERROR_NOT_READABLE_FILE = 3;

    /**
     * @var int
     */
    public const ERROR_CREATE_IMAGE_FROM_STRING = 4;

    /**
     * Initialize a layer from a given image path
     *
     * From an upload form, you can give the "tmp_name" path
     *
     * @throws ImageWorkshopException
     */
    public static function initFromPath(string $path, bool $fixOrientation = false): ImageWorkshopLayer
    {
        if (false === filter_var($path, FILTER_VALIDATE_URL) && !file_exists($path)) {
            throw new ImageWorkshopException(sprintf('File "%s" not exists.', $path), static::ERROR_IMAGE_NOT_FOUND);
        }

        if (false === ($imageSizeInfos = @getImageSize($path))) {
            throw new ImageWorkshopException('Can\'t open the file at "' . $path . '" : file is not readable, did you check permissions (755 / 777) ?', static::ERROR_NOT_READABLE_FILE);
        }

        $mimeContentType = explode('/', $imageSizeInfos['mime']);
        if (!isset($mimeContentType[1])) {
            $givenType = $mimeContentType[1] ?? 'none';
            throw new ImageWorkshopException('Not an image file (jpeg/png/gif) at "'.$path.'" (given format: "'.$givenType.'")', static::ERROR_NOT_AN_IMAGE_FILE);
        }

        $mimeContentType = $mimeContentType[1];
        $exif = array();

        switch ($mimeContentType) {
            case 'jpeg':
                $image = imageCreateFromJPEG($path);

                if (function_exists('exif_read_data') && false !== ($data = @exif_read_data($path))) {
                    $exif = $data;
                }
            break;

            case 'gif':
                $image = imageCreateFromGIF($path);
            break;

            case 'png':
                $image = imageCreateFromPNG($path);
            break;

            case 'webp':
                $image = imageCreateFromWebp($path);
            break;

            default:
                throw new ImageWorkshopException('Not an image file (jpeg/png/gif) at "'.$path.'" (given format: "'.$mimeContentType.'")', static::ERROR_NOT_AN_IMAGE_FILE);
        }

        if (false === $image) {
            throw new ImageWorkshopException('Unable to create image with file found at "'.$path.'"');
        }

        $layer = new ImageWorkshopLayer($image, $exif);

        if ($fixOrientation) {
            $layer->fixOrientation();
        }

        return $layer;
    }

    /**
     * Initialize a text layer
     */
    public static function initTextLayer(string $text, string $fontPath, int $fontSize = 13, string $fontColor = 'ffffff', int $textRotation = 0, string $backgroundColor = null): ImageWorkshopLayer
    {
        $textDimensions = ImageWorkshopLib::getTextBoxDimension($fontSize, $textRotation, $fontPath, $text);

        $layer = static::initVirginLayer($textDimensions['width'], $textDimensions['height'], $backgroundColor);
        $layer->write($text, $fontPath, $fontSize, $fontColor, $textDimensions['left'], $textDimensions['top'], $textRotation);

        return $layer;
    }

    /**
     * Initialize a new virgin layer
     */
    public static function initVirginLayer(int $width = 100, int $height = 100, string $backgroundColor = null): ImageWorkshopLayer
    {
        $opacity = 0;

        if (null === $backgroundColor || $backgroundColor === 'transparent') {
            $opacity = 127;
            $backgroundColor = 'ffffff';
        }

        return new ImageWorkshopLayer(ImageWorkshopLib::generateImage($width, $height, $backgroundColor, $opacity));
    }

    /**
     * Initialize a layer from a resource image var
     */
    public static function initFromResourceVar(GdImage $image): ImageWorkshopLayer
    {
        return new ImageWorkshopLayer($image);
    }

    /**
     * Initialize a layer from a string (obtains with file_get_contents, cURL...)
     *
     * This not recommended to initialize JPEG string with this method, GD displays bugs !
     */
    public static function initFromString(string $imageString): ImageWorkshopLayer
    {
        if (!$image = @imageCreateFromString($imageString)) {
            throw new ImageWorkshopException('Can\'t generate an image from the given string.', static::ERROR_CREATE_IMAGE_FROM_STRING);
        }

        return new ImageWorkshopLayer($image);
    }
}

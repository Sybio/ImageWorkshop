<?php

namespace PHPImageWorkshop;

use PHPImageWorkshop\Core\ImageWorkshopLayer as ImageWorkshopLayer;
use PHPImageWorkshop\Core\ImageWorkshopLib as ImageWorkshopLib;
use PHPImageWorkshop\Exception\ImageWorkshopException as ImageWorkshopException;

// If no autoloader, uncomment these lines:
//require_once(__DIR__.'/Core/ImageWorkshopLayer.php');
//require_once(__DIR__.'/Exception/ImageWorkshopException.php');

/**
 * ImageWorkshop class
 * 
 * Use this class as a factory to initialize ImageWorkshop layers
 *
 * @version 2.0.0
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 */
class ImageWorkshop
{
    /**
     * @var integer
     */
    const ERROR_NOT_AN_IMAGE_FILE = 1;
    
    /**
     * @var integer
     */
    const ERROR_IMAGE_NOT_FOUND = 2;
    
    /**
     * Initialize a layer from a given image path
     * 
     * From an upload form, you can give the "tmp_name" path
     * 
     * @param string $path
     * 
     * @return ImageWorkshopLayer
     */
    public static function initFromPath($path)
    {
        if (file_exists($path) && !is_dir($path)) {
            
            $imageSizeInfos = getImageSize($path);
            $mimeContentType = explode('/', $imageSizeInfos['mime']);
            $mimeContentType = $mimeContentType[1];

            switch ($mimeContentType) {
                case 'jpeg':
                    $image = imageCreateFromJPEG($path);
                break;

                case 'gif':
                    $image = imageCreateFromGIF($path);
                break;

                case 'png':
                    $image = imageCreateFromPNG($path);
                break;

                default:
                    throw new ImageWorkshopException('Not an image file (jpeg/png/gif) at "'.$path.'"', static::ERROR_NOT_AN_IMAGE_FILE);
                break;
            }
            
            return new ImageWorkshopLayer($image);
        }
        
        throw new ImageWorkshopException('No such file found at "'.$path.'"', static::ERROR_IMAGE_NOT_FOUND);
    }
    
    /**
     * Initialize a text layer
     * 
     * @param string $text
     * @param string $fontPath
     * @param integer $fontSize
     * @param string $fontColor
     * @param integer $textRotation
     * @param integer $backgroundColor
     * 
     * @return ImageWorkshopLayer
     */
    public static function initTextLayer($text, $fontPath, $fontSize = 13, $fontColor = 'ffffff', $textRotation = 0, $backgroundColor = null)
    {
        $textDimensions = ImageWorkshopLib::getTextBoxDimension($fontSize, $textRotation, $fontPath, $text);

        $layer = static::initVirginLayer($textDimensions['width'], $textDimensions['height'], $backgroundColor);
        $layer->write($text, $fontPath, $fontSize, $fontColor, $textDimensions['left'], $textDimensions['top'], $textRotation);
        
        return $layer;
    }
    
    /**
     * Initialize a new virgin layer
     * 
     * @param integer $width
     * @param integer $height
     * @param string $backgroundColor
     * 
     * @return ImageWorkshopLayer
     */
    public static function initVirginLayer($width = 100, $height = 100, $backgroundColor = null)
    {
        $opacity = 0;
        
        if (!$backgroundColor || $backgroundColor == 'transparent') {
            $opacity = 127;
            $backgroundColor = 'ffffff';
        }
        
        return new ImageWorkshopLayer(ImageWorkshopLib::generateImage($width, $height, $backgroundColor, $opacity));
    }
    
    /**
     * Initialize a layer from a resource image var
     * 
     * @param \resource $image
     * 
     * @return ImageWorkshopLayer
     */
    public static function initFromResourceVar($image)
    {
        return new ImageWorkshopLayer($image);
    }
    
    /**
     * Initialize a layer from a string (obtains with file_get_contents, cURL...)
     * 
     * This not recommanded to initialize JPEG string with this method, GD displays bugs !
     * 
     * @param string $imageString
     * 
     * @return ImageWorkshopLayer
     */
    public static function initFromString($imageString)
    {
        return new ImageWorkshopLayer(imageCreateFromString($imageString));
    }
}
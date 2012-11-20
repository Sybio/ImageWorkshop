<?php

namespace PHPImageWorkshop\Core;

use PHPImageWorkshop\Core\Exception\ImageWorkshopLibException as ImageWorkshopLibException;

// If no autoloader, uncomment these lines:
//require_once(__DIR__.'/Exception/ImageWorkshopLibException.php');

/**
 * ImageWorkshopLib class
 *
 * Contains some tools to help in some ImageWorkshop calculations
 *
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain  / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 */
class ImageWorkshopLib
{
    /**
     * @var integer
     */
    const ERROR_FONT_NOT_FOUND = 3;
    
    /**
     * Calculate the left top positions of a layer inside a parent layer container
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param integer $containerWidth
     * @param integer $containerHeight
     * @param integer $layerWidth
     * @param integer $layerHeight
     * @param integer $layerPositionX
     * @param integer $layerPositionY
     * @param string $position
     *
     * @return array
     */
    public static function calculatePositions($containerWidth, $containerHeight, $layerWidth, $layerHeight, $layerPositionX, $layerPositionY, $position = 'LT')
    {
        $position = strtolower($position);

        if ($position == 'rt') {

            $layerPositionX = $containerWidth - $layerWidth - $layerPositionX;

        } elseif ($position == 'lb') {

            $layerPositionY = $containerHeight - $layerHeight - $layerPositionY;

        } elseif ($position == 'rb') {

            $layerPositionX = $containerWidth - $layerWidth - $layerPositionX;
            $layerPositionY = $containerHeight - $layerHeight - $layerPositionY;

        } elseif ($position == 'mm') {

            $layerPositionX = (($containerWidth - $layerWidth) / 2) + $layerPositionX;
            $layerPositionY = (($containerHeight - $layerHeight) / 2) + $layerPositionY;

        } elseif ($position == 'mt') {

            $layerPositionX = (($containerWidth - $layerWidth) / 2) + $layerPositionX;

        } elseif ($position == 'mb') {

            $layerPositionX = (($containerWidth - $layerWidth) / 2) + $layerPositionX;
            $layerPositionY = $containerHeight - $layerHeight - $layerPositionY;

        } elseif ($position == 'lm') {

            $layerPositionY = (($containerHeight - $layerHeight) / 2) + $layerPositionY;

        } elseif ($position == 'rm') {

            $layerPositionX = $containerWidth - $layerWidth - $layerPositionX;
            $layerPositionY = (($containerHeight - $layerHeight) / 2) + $layerPositionY;
        }

        return array(
            'x' => $layerPositionX,
            'y' => $layerPositionY,
        );
    }
    
    /**
     * Convert Hex color to RGB color format
     *
     * @param string $hex
     *
     * @return array
     */
    public static function convertHexToRGB($hex)
    {
        return array(
            'R' => (int) base_convert(substr($hex, 0, 2), 16, 10),
            'G' => (int) base_convert(substr($hex, 2, 2), 16, 10),
            'B' => (int) base_convert(substr($hex, 4, 2), 16, 10),
        );
    }
    
    /**
     * Generate a new image resource var
     *
     * @param integer $width
     * @param integer $height
     * @param string $color
     * @param integer $opacity
     *
     * @return resource
     */
    public static function generateImage($width = 100, $height = 100, $color = 'ffffff', $opacity = 127)
    {
        $RGBColors = ImageWorkshopLib::convertHexToRGB($color);

        $image = imagecreatetruecolor($width, $height);
        imagesavealpha($image, true);
        $color = imagecolorallocatealpha($image, $RGBColors['R'], $RGBColors['G'], $RGBColors['B'], $opacity);
        imagefill($image, 0, 0, $color);

        return $image;
    }
    
    /**
     * Return dimension of a text
     *
     * @param $fontSize
     * @param $fontAngle
     * @param $fontFile
     * @param $text
     *
     * @return array or boolean
     */
    public static function getTextBoxDimension($fontSize, $fontAngle, $fontFile, $text)
    {
        if (!file_exists($fontFile)) {
            throw new ImageWorkshopLibException('Can\'t find a font file at this path : "'.$fontFile.'".', static::ERROR_FONT_NOT_FOUND);
        }
        
        $box = imagettfbbox($fontSize, $fontAngle, $fontFile, $text);

		if (!$box) {

			return false;
		}

        $minX = min(array($box[0], $box[2], $box[4], $box[6]));
        $maxX = max(array($box[0], $box[2], $box[4], $box[6]));
        $minY = min(array($box[1], $box[3], $box[5], $box[7]));
        $maxY = max(array($box[1], $box[3], $box[5], $box[7]));
        $width = ($maxX - $minX);
        $height = ($maxY - $minY);
        $left = abs($minX) + $width;
        $top = abs($minY) + $height;

        // to calculate the exact bounding box, we write the text in a large image
        $img = @imagecreatetruecolor($width << 2, $height << 2);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black);

        // for ensure that the text is completely in the image
        imagettftext($img, $fontSize, $fontAngle, $left, $top, $white, $fontFile, $text);

        // start scanning (0=> black => empty)
        $rleft = $w4 = $width<<2;
        $rright = 0;
        $rbottom = 0;
        $rtop = $h4 = $height<<2;

        for ($x = 0; $x < $w4; $x++) {

			for ($y = 0; $y < $h4; $y++) {

				if (imagecolorat($img, $x, $y)) {

					$rleft = min($rleft, $x);
					$rright = max($rright, $x);
					$rtop = min($rtop, $y);
					$rbottom = max($rbottom, $y);
				}
			}
		}

        imagedestroy($img);

        return array(
            'left' => $left - $rleft,
            'top' => $top - $rtop,
            'width' => $rright - $rleft + 1,
            'height' => $rbottom - $rtop + 1,
        );
    }
    
    /**
     * Copy an image on another one and converse transparency
     *
     * @param resource $destImg
     * @param resource $srcImg
     * @param integer $destX
     * @param integer $destY
     * @param integer $srcX
     * @param integer $srcY
     * @param integer $srcW
     * @param integer $srcH
     * @param integer $pct
     */
    public static function imageCopyMergeAlpha(&$destImg, &$srcImg, $destX, $destY, $srcX, $srcY, $srcW, $srcH, $pct = 0)
    {
        $destX = (int) $destX;
        $destY = (int) $destY;
        $srcX = (int) $srcX;
        $srcY = (int) $srcY;
        $srcW = (int) $srcW;
        $srcH = (int) $srcH;
        $pct = (int) $pct;
        $destW = imageSX($destImg);
        $destH = imageSY($destImg);

        for ($y = 0; $y < $srcH + $srcY; $y++) {

            for ($x = 0; $x < $srcW + $srcX; $x++) {

                if ($x + $destX >= 0 && $x + $destX < $destW && $x + $srcX >= 0 && $x + $srcX < $srcW && $y + $destY >= 0 && $y + $destY < $destH && $y + $srcY >= 0 && $y + $srcY < $srcH) {

                    $destPixel = imageColorsForIndex($destImg, imageColorat($destImg, $x + $destX, $y + $destY));
                    $srcImgColorat = imageColorat($srcImg, $x + $srcX, $y + $srcY);
                    
                    if ($srcImgColorat > 0) {
                    
                        $srcPixel = imageColorsForIndex($srcImg, $srcImgColorat);
    
                        $srcAlpha = 1 - ($srcPixel['alpha'] / 127);
                        $destAlpha = 1 - ($destPixel['alpha'] / 127);
                        $opacity = $srcAlpha * $pct / 100;
    
                        if ($destAlpha >= $opacity) {
    						$alpha = $destAlpha;
    					}
    
                        if ($destAlpha < $opacity) {
    						$alpha = $opacity;
    					}
    
                        if ($alpha > 1) {
    						$alpha = 1;
    					}
    
                        if ($opacity > 0) {
                            
                            $destRed = round((($destPixel['red'] * $destAlpha * (1 - $opacity))));
                            $destGreen = round((($destPixel['green'] * $destAlpha * (1 - $opacity))));
                            $destBlue = round((($destPixel['blue'] * $destAlpha * (1 - $opacity))));
                            $srcRed = round((($srcPixel['red'] * $opacity)));
                            $srcGreen = round((($srcPixel['green'] * $opacity)));
                            $srcBlue = round((($srcPixel['blue'] * $opacity)));
                            $red = round(($destRed + $srcRed  ) / ($destAlpha * (1 - $opacity) + $opacity));
                            $green = round(($destGreen + $srcGreen) / ($destAlpha * (1 - $opacity) + $opacity));
                            $blue = round(($destBlue + $srcBlue ) / ($destAlpha * (1 - $opacity) + $opacity));
    
                            if ($red   > 255) {
    							$red   = 255;
    						}
    
                            if ($green > 255) {
    							$green = 255;
                            }
    
    						if ($blue  > 255) {
    							$blue  = 255;
    						}
    
                            $alpha = round((1 - $alpha) * 127);
                            $color = imageColorAllocateAlpha($destImg, $red, $green, $blue, $alpha);
                            imageSetPixel($destImg, $x + $destX, $y + $destY, $color);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Merge two image var
     *
     * @param resource $destinationImage
     * @param resource $sourceImage
     * @param integer $destinationPosX
     * @param integer $destinationPosY
     * @param integer $sourcePosX
     * @param integer $sourcePosY
     */
    public static function mergeTwoImages(&$destinationImage, $sourceImage, $destinationPosX = 0, $destinationPosY = 0, $sourcePosX = 0, $sourcePosY = 0)
    {
        imageCopy($destinationImage, $sourceImage, $destinationPosX, $destinationPosY, $sourcePosX, $sourcePosY, imageSX($sourceImage), imageSY($sourceImage));
    }
}
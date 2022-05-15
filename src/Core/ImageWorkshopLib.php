<?php

namespace PHPImageWorkshop\Core;

use GdImage;
use PHPImageWorkshop\Core\Exception\ImageWorkshopLibException;

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
     * @var int
     */
    public const ERROR_FONT_NOT_FOUND = 3;

    /**
     * Calculate the left top positions of a layer inside a parent layer container
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @return array{x: int, y: int}
     */
    public static function calculatePositions(int $containerWidth, int $containerHeight, int $layerWidth, int $layerHeight, int $layerPositionX, int $layerPositionY, string $position = 'LT'): array
    {
        $position = strtolower($position);

        if ($position === 'rt') {
            $layerPositionX = $containerWidth - $layerWidth - $layerPositionX;
        } elseif ($position === 'lb') {
            $layerPositionY = $containerHeight - $layerHeight - $layerPositionY;
        } elseif ($position === 'rb') {
            $layerPositionX = $containerWidth - $layerWidth - $layerPositionX;
            $layerPositionY = $containerHeight - $layerHeight - $layerPositionY;
        } elseif ($position === 'mm') {
            $layerPositionX = (int) ((($containerWidth - $layerWidth) / 2) + $layerPositionX);
            $layerPositionY = (int) ((($containerHeight - $layerHeight) / 2) + $layerPositionY);
        } elseif ($position === 'mt') {
            $layerPositionX = (int) ((($containerWidth - $layerWidth) / 2) + $layerPositionX);
        } elseif ($position === 'mb') {
            $layerPositionX = (int) ((($containerWidth - $layerWidth) / 2) + $layerPositionX);
            $layerPositionY = $containerHeight - $layerHeight - $layerPositionY;
        } elseif ($position === 'lm') {
            $layerPositionY = (int) ((($containerHeight - $layerHeight) / 2) + $layerPositionY);
        } elseif ($position === 'rm') {
            $layerPositionX = $containerWidth - $layerWidth - $layerPositionX;
            $layerPositionY = (int) ((($containerHeight - $layerHeight) / 2) + $layerPositionY);
        }

        return array(
            'x' => $layerPositionX,
            'y' => $layerPositionY,
        );
    }

    /**
     * Convert Hex color to RGB color format
     *
     * @return array{R: int, G: int, B: int}
     */
    public static function convertHexToRGB(?string $hex): array
    {
        return array(
            'R' => (int) base_convert(substr($hex ?? '', 0, 2), 16, 10),
            'G' => (int) base_convert(substr($hex ?? '', 2, 2), 16, 10),
            'B' => (int) base_convert(substr($hex ?? '', 4, 2), 16, 10),
        );
    }

    /**
     * Generate a new image
     */
    public static function generateImage(int $width = 100, int $height = 100, string $color = 'ffffff', int $opacity = 127): GdImage
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
     * @return array{left: int, top: int, width: int, height: int}|false
     */
    public static function getTextBoxDimension(float $fontSize, float $fontAngle, string $fontFile, string $text): array|bool
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
     */
    public static function imageCopyMergeAlpha(GdImage $destImg, GdImage $srcImg, int $destX, int $destY, int $srcX, int $srcY, int $srcW, int $srcH, int $pct = 0): void
    {
        $destW = imageSX($destImg);
        $destH = imageSY($destImg);
        $alpha = 0;

        for ($y = 0; $y < $srcH + $srcY; $y++) {
            for ($x = 0; $x < $srcW + $srcX; $x++) {
                if ($x + $destX >= 0 && $x + $destX < $destW && $x + $srcX >= 0 && $x + $srcX < $srcW && $y + $destY >= 0 && $y + $destY < $destH && $y + $srcY >= 0 && $y + $srcY < $srcH) {
                    $destPixel = imageColorsForIndex($destImg, imageColorat($destImg, $x + $destX, $y + $destY));
                    $srcImgColorat = imageColorat($srcImg, $x + $srcX, $y + $srcY);

                    if ($srcImgColorat >= 0) {
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
                            $red = round(($destRed + $srcRed) / ($destAlpha * (1 - $opacity) + $opacity));
                            $green = round(($destGreen + $srcGreen) / ($destAlpha * (1 - $opacity) + $opacity));
                            $blue = round(($destBlue + $srcBlue) / ($destAlpha * (1 - $opacity) + $opacity));

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
                            $color = imageColorAllocateAlpha($destImg, $red, $green, $blue, (int) $alpha);
                            imageSetPixel($destImg, $x + $destX, $y + $destY, $color);
                        }
                    }
                }
            }
        }
    }

    /**
     * Merge two image var
     */
    public static function mergeTwoImages(GdImage $destinationImage, GdImage $sourceImage, int $destinationPosX = 0, int $destinationPosY = 0, int $sourcePosX = 0, int $sourcePosY = 0): void
    {
        imageCopy($destinationImage, $sourceImage, $destinationPosX, $destinationPosY, $sourcePosX, $sourcePosY, imageSX($sourceImage), imageSY($sourceImage));
    }
}

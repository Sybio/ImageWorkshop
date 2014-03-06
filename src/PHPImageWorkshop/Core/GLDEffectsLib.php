<?php

namespace PHPImageWorkshop\Core;

use PHPImageWorkshop\ImageWorkshop as ImageWorkshop;
use PHPImageWorkshop\Core\ImageWorkshopLib as ImageWorkshopLib;
use PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException as ImageWorkshopLayerException;

// If no autoloader, uncomment these lines:
require_once(__DIR__.'/../ImageWorkshop.php');
require_once(__DIR__.'/ImageWorkshopLib.php');
require_once(__DIR__.'/Exception/ImageWorkshopLayerException.php');

/**
 * ImageWorkshopLayer class
 *
 * This class represents a layer that contains a background image and can be edited
 *
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 */
class LayerEffects{
 /**
     * Apply a filter on the layer
     * Be careful: some filters can damage transparent images, use it sparingly ! (A good pratice is to use mergeAll on your layer before applying a filter)
     *
     * @param int $filterType (http://www.php.net/manual/en/function.imagefilter.php)
     * @param int $arg1
     * @param int $arg2
     * @param int $arg3
     * @param int $arg4
     * @param boolean $recursive
     */
    public function applyFilter($img, $filterType, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $recursive = false)
    {
    	switch (filterType) {
    		case 'IMG_FILTER_COLORIZE':
    			imagefilter($img, $filterType, $arg1, $arg2, $arg3, $arg4);
    			break;

    		case 'IMG_FILTER_BRIGHTNESS':
    		case 'IMG_FILTER_CONTRAST':
    		case 'IMG_FILTER_SMOOTH':
    			imagefilter($img, $filterType, $arg1, $arg2, $arg3, $arg4);
    			break;

    		case 'IMG_FILTER_PIXELATE':
    			imagefilter($img, $filterType, $arg1, $arg2, $arg3, $arg4);
    			break;
    		default:
    			imagefilter($img, $filterType);...
    			break;

    		return $img;
    	}


    }

    /**
     * Apply a image convolution on the layer
     * 
     *
     * @param array $matrix 
     * @param int $div
     * @param int $offset
     * @param boolean $recursive
     *
     * @author Email Goodlittledeveloper@gmail.com
     *
     */
    public function applyimageconvolution($layer, $matrix, $div=0, $offset=0){
        if(is_string($matrix)){
            $matrix = strtolower($matrix);
            switch($matrix){
                case "blur":
                    $matrix = array(array(1,1,1), array(1,.25,1), array(1,1,1));
                    $div = 8.25;
                    $offset = 0;
                break;

                case "emboss": 
                    $matrix = array(array(2, 2, 2), array(2, 1, -2), array(-2, -2, -2));
                    $div = 0;
                    $offset = 127;
                break;
                    
                case  "gaussian blur" :
                    $matrix = array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0));
                    $div = 16;
                    $offset = 0;
                break;

                case "sharpen" :
                    $Matrix = array(array(-1.2, -1.2, -1.2),array(-1.2, .4, -1.2),array(-1.2, -1.2, -1.2)); 
                    $div = array_sum(array_map('array_sum', $Matrix));  
                    $offset = 0;
                break;

            }
             
        }
        if(is_array($matrix)){
           $img = imageconvolution($layer->getResult(), $matrix, $div, $offset);
           return ImageWorkshop::initFromResourceVar($img);
        }

        

        

    }

     public function toGreyscale($layer,$type = null){
        $type = strtolower($type);
        $image = $layer->getResult();
        $width = $layer->getWidth;
        $height = $layer->getHeight;
        
        switch ($type) {
            case 'lightness':
                for ($h=0; $h<$height; $h++){
                    for ($w=0; $w<$width; $w++){
                        $rgb = imagecolorat($image, $w, $h);
                         $r  = ($rgb >> 16) & 0xFF;
                         $g  = ($rgb >> 8) & 0xFF;
                         $b  =  $rgb & 0xFF;
                         $a  = ($rgb & 0x7F000000) >> 24;

                         $grey = (max($r,$g,$b)+min($r,$g,$b))/2;

                         imagesetpixel($image,$w,$h,imagecolorallocatealpha($image,$grey,$grey,$grey,$a));
                    }
                }

                break;

            case 'luminosity':
                for ($h=0; $h<$height; $h++){
                    for ($w=0; $w<$width; $w++){
                         $rgb = imagecolorat($image, $w, $h);
                         $r  = ($rgb >> 16) & 0xFF;
                         $g  = ($rgb >> 8) & 0xFF;
                         $b  =  $rgb & 0xFF;
                         $a  = ($rgb & 0x7F000000) >> 24;

                         $grey = 0.21*$r+0.71*$g+0.07*$b;

                         imagesetpixel($image,$w,$h,imagecolorallocatealpha($image,$grey,$grey,$grey,$a));
                    }
                }
                break;
            
            default://average
                for ($h=0; $h<$height; $h++){
                    for ($w=0; $w<$width; $w++){
                        $rgb = imagecolorat($image, $w, $h);
                         $r  = ($rgb >> 16) & 0xFF;
                         $g  = ($rgb >> 8) & 0xFF;
                         $b  =  $rgb & 0xFF;
                         $a  = ($rgb & 0x7F000000) >> 24;

                         $grey = ($r+$g+$b)/3;

                         imagesetpixel($image,$w,$h,imagecolorallocatealpha($image,$grey,$grey,$grey,$a));
                    }
                }
                break;
        }

    }

    /**
     * Apply alpha layer mask.
     *
     * @param layer $mask 
     *
     * @author email goodlittledeveloper@gmail.com
     *
     */

    public function applyalphamask($layer, $mask){
        
        $masktemp = clone $mask;

        $masktemp->resizeInPixel($layer->width, $layer->height); // make $mask and $layer the same size.
        $masktemp->applyFilter(IMG_FILTER_GRAYSCALE); //converts to greyscale if not greyscale;
        $masktemp->applyFilter(IMG_FILTER_NEGATE); // inverts the mask so black  = 100% transparent and white = 0

        $layerImg= $layer->getImage();
        $maskImg = $masktemp->getImage();

        $imgtemp = imageCreateTrueColor($layer->width,$layer->height);

        imagealphablending($imgtemp, false);
        imagesavealpha($imgtemp, true);


        for ($h=0; $h<$this->height; $h++){
            for ($w=0; $w<$this->width; $w++){
                $Lrgb = imagecolorat($layerImg, $w, $h);
                $Lcolors = imagecolorsforindex($layerImg, $Lrgb);


                $alpha = (imagecolorat($maskImg, $w, $h) >> 16) & 0xFF; //faster calc to get red value.          
                $alpha = $alpha/255*127; // the gets alpha from red value 
                
                imagesetpixel($imgtemp,$w,$h,imagecolorallocatealpha($imgtemp,$Lcolors["red"],$Lcolors["green"],$Lcolors["blue"],$alpha));
        }}

       

        
        unset($this->image);
        return $imgtemp;
        unset($imgtemp);    
        
    }

     /**
     * split Layer in to channels.
     *
     * @return a group of 4 layers each one a channel.
     *
     * @author email goodlittledeveloper@gmail.com
     *
     */

    public function splitchannels($layer){
        if($layer->getLastLayerId()!=0){
            throw new ImageWorkshopException('Can\'t split channels of a layer group', static::ERROR_LAYER_GROUP);
        }
        else{

        $ChlR = imageCreateTrueColor($layer->width,$layer->height);
        $ChlG = imageCreateTrueColor($layer->width,$layer->height);
        $ChlB = imageCreateTrueColor($layer->width,$layer->height);
        $ChlA = imageCreateTrueColor($layer->width,$layer->height);

        $group =  ImageWorkshop::initVirginLayer($layer->width,$layer->height);
        $image = $layer->getImage();

        for ($h=0; $h<$layer->height; $h++){
            for ($w=0; $w<$layer->width; $w++){
                $Lrgb = imagecolorat($image, $w, $h);
                $Lcolors = imagecolorsforindex($image, $Lrgb);

                
                imagesetpixel($ChlR,$w,$h,imagecolorallocatealpha($ChlR,$Lcolors["red"],0,0,0));
                imagesetpixel($ChlG,$w,$h,imagecolorallocatealpha($ChlG,0,$Lcolors["green"],0,0));
                imagesetpixel($ChlB,$w,$h,imagecolorallocatealpha($ChlB,0,0,$Lcolors["blue"],0));
                imagesetpixel($ChlA,$w,$h,imagecolorallocatealpha($ChlA,127,127,127,$Lcolors["alpha"]));
        }}

        
        $r     =  ImageWorkshop::initFromResourceVar($ChlR);
        $g     =  ImageWorkshop::initFromResourceVar($ChlG);
        $b     =  ImageWorkshop::initFromResourceVar($ChlB);
        $a     =  ImageWorkshop::initFromResourceVar($ChlA);

        unset($ChlR);
        unset($ChlG);    
        unset($ChlB);
        unset($ChlA);
           

        $sublayerInfos = $group->addLayer("1", $r, 0, 0, "LT");
        $sublayerInfos = $group->addLayer("2", $g, 0, 0, "LT");
        $sublayerInfos = $group->addLayer("3", $b, 0, 0, "LT");
        $sublayerInfos = $group->addLayer("4", $a, 0, 0, "LT");

        return $group;
    }}

     /**
     * get channel by color.
     *
     * @param string of channel name
     *
     * @return a layer based on channel name a channel.
     *
     * @author email goodlittledeveloper@gmail.com
     *
     */

    public function getchannel($layer,$channel){
        switch ($channel) {
            case 'red':
                return $layer->getLayer(1);
                break;

            case 'green':
                return $layer->getLayer(2);
                break;

            case 'blue':
                return $layer->getLayer(3);
                break;

            case 'alpha':
                return $layer->getLayer(4);
                break;

        }
    }

    /**
     * merge channels.
     *
     * @param group of channels
     *
     * @return sets layer image to merge channels.
     *
     * @author email goodlittledeveloper@gmail.com
     *
     */

    public function mergechannels($group){
        $ChlR = $group->getLayer(1)->getResult();
        $ChlG = $group->getLayer(2)->getResult();
        $ChlB = $group->getLayer(3)->getResult();
        $ChlA = $group->getLayer(4)->getResult();

        $imgtemp = imageCreateTrueColor($group->width,$group->height);

        imagealphablending($imgtemp, false);
        imagesavealpha($imgtemp, true);

        for ($h=0; $h<$this->height; $h++){
            for ($w=0; $w<$this->width; $w++){
                
                $Color["red"]   = (imagecolorat($ChlR, $w, $h) >> 16) & 0xFF;
                $Color["green"] = (imagecolorat($ChlG, $w, $h) >> 8) & 0xFF;
                $Color["blue"]  =  imagecolorat($ChlB, $w, $h) & 0xFF;
                $Color["alpha"] = (imagecolorat($ChlA, $w, $h) & 0x7F000000) >> 24;
          
                imagesetpixel($imgtemp,$w,$h,imagecolorallocatealpha($imgtemp,$Color["red"],$Color["green"],$Color["blue"],$Color["alpha"]));
            }
        }

        unset($ChlR);
        unset($ChlG);    
        unset($ChlB);
        unset($ChlA);
        unset($group);

        return $imgtemp;
        unset($imgtemp);      
    }
}

?>
<?php

namespace PHPImageWorkshop\Core;

use PHPImageWorkshop\Exif\ExifOrientations;
use PHPImageWorkshop\ImageWorkshop as ImageWorkshop;
use PHPImageWorkshop\Core\ImageWorkshopLib as ImageWorkshopLib;
use PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException as ImageWorkshopLayerException;

// If no autoloader, uncomment these lines:
//require_once(__DIR__.'/../ImageWorkshop.php');
//require_once(__DIR__.'/ImageWorkshopLib.php');
//require_once(__DIR__.'/Exception/ImageWorkshopLayerException.php');

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
class ImageWorkshopLayer
{
    // ===================================================================================
    // Properties
    // ===================================================================================

    /**
     * @var width
     *
     * Width of the group model
     * Default: 800
     */
    protected $width;

    /**
     * @var height
     *
     * Height of the group model
     * Default: 600
     */
    protected $height;

    /**
     * @var layers
     *
     * Layers (and groups)
     */
    public $layers;

    /**
     * @var layerLevels
     *
     * Levels of the sublayers in the stack
     */
    protected $layerLevels;

    /**
     * @var layerPositions
     *
     * Positions (x and y) of the sublayers in the stack
     */
    protected $layerPositions;

    /**
     * @var lastLayerId
     *
     * Id of the last indexed sublayer in the stack
     */
    protected $lastLayerId;

    /**
     * @var highestLayerLevel
     *
     * The highest sublayer level
     */
    protected $highestLayerLevel;

    /**
     * @var image
     *
     * Background Image
     */
    protected $image;

    /**
     * @var array
     *
     * Exif data
     */
    protected $exif;
    
    /**
     * @var string
     */
    const UNIT_PIXEL = 'pixel';
    
    /**
     * @var string
     */    
    const UNIT_PERCENT = 'percent';
    
    /**
     * @var integer
     */
    const ERROR_GD_NOT_INSTALLED = 1;
    
    /**
     * @var integer
     */
    const ERROR_PHP_IMAGE_VAR_NOT_USED = 2;
    
    /**
     * @var integer
     */
    const ERROR_FONT_NOT_FOUND = 3;
    
    /**
     * @var integer
     */
    const METHOD_DEPRECATED = 4;
    
    /**
     * @var integer
     */
    const ERROR_NEGATIVE_NUMBER_USED = 5;

    /**
     * @var integer
     */
    const ERROR_NOT_WRITABLE_FOLDER = 6;

    /**
     * @var integer
     */
    const ERROR_NOT_SUPPORTED_FORMAT = 7;

    /**
     * @var integer
     */
    const ERROR_UNKNOW = 8;
    
    // ===================================================================================
    // Methods
    // ===================================================================================
    
    // Magicals
    // =========================================================
    
    /**
     * Constructor
     *
     * @param \resource $image
     */
    public function __construct($image, array $exif = array())
    {
        if (!extension_loaded('gd')) {
            throw new ImageWorkshopLayerException('PHPImageWorkshop requires the GD extension to be loaded.', static::ERROR_GD_NOT_INSTALLED);
        }
    	
        if (gettype($image) != 'resource' && gettype($image) != '\resource') {
            throw new ImageWorkshopLayerException('You must give a php image var to initialize a layer.', static::ERROR_PHP_IMAGE_VAR_NOT_USED);
        }

        $this->width = imagesx($image);
        $this->height = imagesy($image);
        $this->image = $image;
        $this->exif = $exif;
        $this->layers = $this->layerLevels = $this->layerPositions = array();
        $this->clearStack();
    }

    /**
     * Clone method: use it if you want to reuse an existing ImageWorkshop object in another variable
     * This is important because img resource var references all the same image in PHP.
     * Example: $b = clone $a; (never do $b = $a;)
     */
    public function __clone()
    {
        $this->createNewVarFromBackgroundImage();
    }
    
    // Superimpose a sublayer
    // =========================================================
    
    /**
     * Add an existing ImageWorkshop sublayer and set it in the stack at a given level
     * Return an array containing the generated sublayer id in the stack and its corrected level:
     * array("layerLevel" => integer, "id" => integer)
     *
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param integer $layerLevel
     * @param ImageWorkshopLayer $layer
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     *
     * @return array
     */
    public function addLayer($layerLevel, $layer, $positionX = 0, $positionY = 0, $position = 'LT')
    {
        return $this->indexLayer($layerLevel, $layer, $positionX, $positionY, $position);
    }

    /**
     * Add an existing ImageWorkshop sublayer and set it in the stack at the highest level
     * Return an array containing the generated sublayer id in the stack and the highest level:
     * array("layerLevel" => integer, "id" => integer)
     *
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param ImageWorkshopLayer $layer
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     *
     * @return array
     */
    public function addLayerOnTop($layer, $positionX = 0, $positionY = 0, $position = 'LT')
    {
        return $this->indexLayer($this->highestLayerLevel + 1, $layer, $positionX, $positionY, $position);
    }

    /**
     * Add an existing ImageWorkshop sublayer and set it in the stack at level 1
     * Return an array containing the generated sublayer id in the stack and level 1:
     * array("layerLevel" => integer, "id" => integer)
     *
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param ImageWorkshopLayer $layer
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     *
     * @return array
     */
    public function addLayerBelow($layer, $positionX = 0, $positionY = 0, $position = 'LT')
    {
        return $this->indexLayer(1, $layer, $positionX, $positionY, $position);
    }
    
    // Move a sublayer inside the stack
    // =========================================================
    
    /**
     * Move a sublayer on the top of a group stack
     * Return new sublayer level if success or false otherwise
     *
     * @param integer $layerId
     * @return mixed
     */
    public function moveTop($layerId)
    {
        return $this->moveTo($layerId, $this->highestLayerLevel, false);
    }

    /**
     * Move a sublayer to the level 1 of a group stack
     * Return new sublayer level if success or false otherwise
     *
     * @param integer $layerId
     * @param integer $level
     *
     * @return mixed
     */
    public function moveBottom($layerId)
    {
        return $this->moveTo($layerId, 1, true);
    }

    /**
     * Move a sublayer to the level $level of a group stack
     * Return new sublayer level if success or false if layer isn't found
     *
     * Set $insertUnderTargetedLayer true if you want to move the sublayer under the other sublayer at the targeted level,
     * or false to insert it on the top of the other sublayer at the targeted level
     *
     * @param integer $layerId
     * @param integer $level
     * @param boolean $insertUnderTargetedLayer
     *
     * @return mixed
     */
    public function moveTo($layerId, $level, $insertUnderTargetedLayer = true)
    {
        // if the sublayer exists in stack
        if ($this->isLayerInIndex($layerId)) {

            $layerOldLevel = $this->getLayerLevel($layerId);

            if ($level < 1) {
                $level = 1;
                $insertUnderTargetedLayer = true;
            }

            if ($level > $this->highestLayerLevel) {

                $level = $this->highestLayerLevel;
                $insertUnderTargetedLayer = false;
            }

            // Not the same level than the current level
            if ($layerOldLevel != $level) {

                $isUnderAndNewLevelHigher = $isUnderAndNewLevelLower = $isOnTopAndNewLevelHigher = $isOnTopAndNewLevelLower = false;

                if ($insertUnderTargetedLayer) { // Under level

                    if ($level > $layerOldLevel) { // new level higher

                        $incrementorStartingValue = $layerOldLevel;
                        $stopLoopWhenSmallerThan = $level;
                        $isUnderAndNewLevelHigher = true;

                    } else { // new level lower

                        $incrementorStartingValue = $level;
                        $stopLoopWhenSmallerThan = $layerOldLevel;
                        $isUnderAndNewLevelLower = true;
                    }

                } else { // on the top

                    if ($level > $layerOldLevel) { // new level higher

                        $incrementorStartingValue = $layerOldLevel;
                        $stopLoopWhenSmallerThan = $level;
                        $isOnTopAndNewLevelHigher = true;

                    } else { // new level lower

                        $incrementorStartingValue = $level;
                        $stopLoopWhenSmallerThan = $layerOldLevel;
                        $isOnTopAndNewLevelLower = true;
                    }
                }

                ksort($this->layerLevels);
                $layerLevelsTmp = $this->layerLevels;

                if ($isOnTopAndNewLevelLower) {
                    $level++;
                }

                for ($i = $incrementorStartingValue; $i < $stopLoopWhenSmallerThan; $i++) {

                    if ($isUnderAndNewLevelHigher || $isOnTopAndNewLevelHigher) {

                        $this->layerLevels[$i] = $layerLevelsTmp[$i + 1];

                    } else {

                        $this->layerLevels[$i + 1] = $layerLevelsTmp[$i];
                    }
                }

                unset($layerLevelsTmp);

                if ($isUnderAndNewLevelHigher) {
                    $level--;
                }

                $this->layerLevels[$level] = $layerId;

                return $level;

            } else {
                return $level;
            }
        }

        return false;
    }

    /**
     * Move up a sublayer in the stack (level +1)
     * Return new sublayer level if success, false otherwise
     *
     * @param integer $layerId
     *
     * @return mixed
     */
    public function moveUp($layerId)
    {
        if ($this->isLayerInIndex($layerId)) { // if the sublayer exists in the stack
            $layerOldLevel = $this->getLayerLevel($layerId);
            return $this->moveTo($layerId, $layerOldLevel + 1, false);
        }

        return false;
    }

    /**
     * Move down a sublayer in the stack (level -1)
     * Return new sublayer level if success, false otherwise
     *
     * @param integer $layerId
     *
     * @return mixed
     */
    public function moveDown($layerId)
    {
        if ($this->isLayerInIndex($layerId)) { // if the sublayer exists in the stack
            $layerOldLevel = $this->getLayerLevel($layerId);
            return $this->moveTo($layerId, $layerOldLevel - 1, true);
        }

        return false;
    }
    
    // Merge layers
    // =========================================================
    
    /**
     * Merge a sublayer with another sublayer below it in the stack
     * Note: the result layer will conserve the given id
     * Return true if success or false if layer isn't found or doesn't have a layer under it in the stack
     *
     * @param integer $layerId
     *
     * @return boolean
     */
    public function mergeDown($layerId)
    {
        // if the layer exists in document
        if ($this->isLayerInIndex($layerId)) {

            $layerLevel = $this->getLayerLevel($layerId);
            $layerPositions = $this->getLayerPositions($layerId);
            $layer = $this->getLayer($layerId);
            $layerWidth = $layer->getWidth();
            $layerHeight = $layer->getHeight();
            $layerPositionX = $this->layerPositions[$layerId]['x'];
            $layerPositionY = $this->layerPositions[$layerId]['y'];

            if ($layerLevel > 1) {

                $underLayerId = $this->layerLevels[$layerLevel - 1];
                $underLayer = $this->getLayer($underLayerId);
                $underLayerWidth = $underLayer->getWidth();
                $underLayerHeight = $underLayer->getHeight();
                $underLayerPositionX = $this->layerPositions[$underLayerId]['x'];
                $underLayerPositionY = $this->layerPositions[$underLayerId]['y'];

                $totalWidthLayer = $layerWidth + $layerPositionX;
                $totalHeightLayer = $layerHeight + $layerPositionY;

                $totalWidthUnderLayer = $underLayerWidth + $underLayerPositionX;
                $totalHeightUnderLayer = $underLayerHeight + $underLayerPositionY;

                $minLayerPositionX = $layerPositionX;

                if ($layerPositionX > $underLayerPositionX) {
                    $minLayerPositionX = $underLayerPositionX;
                }

                $minLayerPositionY = $layerPositionY;

                if ($layerPositionY > $underLayerPositionY) {
                    $minLayerPositionY = $underLayerPositionY;
                }

                if ($totalWidthLayer > $totalWidthUnderLayer) {
                    $layerTmpWidth = $totalWidthLayer - $minLayerPositionX;
                } else {
                    $layerTmpWidth = $totalWidthUnderLayer - $minLayerPositionX;
                }

                if ($totalHeightLayer > $totalHeightUnderLayer) {
                    $layerTmpHeight = $totalHeightLayer - $minLayerPositionY;
                } else {
                    $layerTmpHeight = $totalHeightUnderLayer - $minLayerPositionY;
                }

                $layerTmp = ImageWorkshop::initVirginLayer($layerTmpWidth, $layerTmpHeight);

                $layerTmp->addLayer(1, $underLayer, $underLayerPositionX - $minLayerPositionX, $underLayerPositionY - $minLayerPositionY);
                $layerTmp->addLayer(2, $layer, $layerPositionX - $minLayerPositionX, $layerPositionY - $minLayerPositionY);

                // Update layers
                $layerTmp->mergeAll();
                $this->layers[$underLayerId] = clone $layerTmp;
                $this->changePosition($underLayerId, $minLayerPositionX, $minLayerPositionX);

            } else {

                $layerTmp = ImageWorkshop::initFromResourceVar($this->image);
                $layerTmp->addLayer(1, $layer, $layerPositionX, $layerPositionY);

                $this->image = $layerTmp->getResult(); // Update background image
            }

            unset($layerTmp);
            $this->remove($layerId); // Remove the merged layer from the stack

            return true;
        }
        
        return false;
    }

    /**
     * Merge sublayers in the stack on the layer background
     */
    public function mergeAll()
    {
        $this->image = $this->getResult();
        $this->clearStack();
    }
    
    /**
     * Paste an image on the layer
     * You can specify the position left (in pixels) and the position top (in pixels) of the added image relatives to the layer
     * Otherwise, it will be set at 0 and 0
     *
     * @param string $unit Use one of `UNIT_*` constants, "UNIT_PIXEL" by default
     * @param resource $image
     * @param integer $positionX
     * @param integer $positionY
     */
    public function pasteImage($unit = self::UNIT_PIXEL, $image, $positionX = 0, $positionY = 0)
    {
        if ($unit == self::UNIT_PERCENT) {

            $positionX = round(($positionX / 100) * $this->width);
            $positionY = round(($positionY / 100) * $this->height);
        }

        imagecopy($this->image, $image, $positionX, $positionY, 0, 0, $image->getWidth(), $image->getHeight());
    }
    
    // Change sublayer positions
    // =========================================================
    
    /**
     * Change the position of a sublayer for new positions
     *
     * @param integer $layerId
     * @param integer $newPosX
     * @param integer $newPosY
     *
     * @return boolean
     */
    public function changePosition($layerId, $newPosX = null, $newPosY = null)
    {
        // if the sublayer exists in the stack
        if ($this->isLayerInIndex($layerId)) {
            
            if ($newPosX !== null) {
                $this->layerPositions[$layerId]['x'] = $newPosX;
            }
            
            if ($newPosY !== null) {
                $this->layerPositions[$layerId]['y'] = $newPosY;
            }
            
            return true;
        }

        return false;
    }
    
    /**
     * Apply a translation on a sublayer that change its positions
     *
     * @param integer $layerId
     * @param integer $addedPosX
     * @param integer $addedPosY
     *
     * @return mixed (array of new positions or false if fail)
     */
    public function applyTranslation($layerId, $addedPosX = null, $addedPosY = null)
    {
        // if the sublayer exists in the stack
        if ($this->isLayerInIndex($layerId)) {
            
            if ($addedPosX !== null) {
                $this->layerPositions[$layerId]['x'] += $addedPosX;
            }
            
            if ($addedPosY !== null) {
                $this->layerPositions[$layerId]['y'] += $addedPosY;
            }
            
            return $this->layerPositions[$layerId];
        }

        return false;
    }
    
    // Removing sublayers
    // =========================================================
    
    /**
     * Delete a layer (return true if success, false if no sublayer is found)
     *
     * @param integer $layerId
     *
     * @return boolean
     */
    public function remove($layerId)
    {
        // if the layer exists in document
        if ($this->isLayerInIndex($layerId)) {

            $layerToDeleteLevel = $this->getLayerLevel($layerId);

            // delete
            $this->layers[$layerId]->delete();
            unset($this->layers[$layerId]);
            unset($this->layerLevels[$layerToDeleteLevel]);
            unset($this->layerPositions[$layerId]);

            // One or plural layers are sub of the deleted layer
            if (array_key_exists(($layerToDeleteLevel + 1), $this->layerLevels)) {

                ksort($this->layerLevels);

                $layerLevelsTmp = $this->layerLevels;

                $maxOldestLevel = 1;
                foreach ($layerLevelsTmp as $levelTmp => $layerIdTmp) {

                    if ($levelTmp > $layerToDeleteLevel) {
                        $this->layerLevels[($levelTmp - 1)] = $layerIdTmp;
                    }

                    $maxOldestLevel++;
                }
                unset($layerLevelsTmp);
                unset($this->layerLevels[$maxOldestLevel]);
            }

            $this->highestLayerLevel--;

            return true;
        }

        return false;
    }
    
    /**
     * Reset the layer stack
     * 
     * @boolean $deleteSubImgVar Delete sublayers image resource var
     */
    public function clearStack($deleteSubImgVar = true)
    {
        if ($deleteSubImgVar) {
            foreach ($this->layers as $layer) {
                $layer->delete();
            }
        }
        
        unset($this->layers);
        unset($this->layerLevels);
        unset($this->layerPositions);

        $this->lastLayerId = 0;
        $this->layers = array();
        $this->layerLevels = array();
        $this->layerPositions = array();
        $this->highestLayerLevel = 0;
    }
    
    // Perform an action
    // =========================================================
    
    /**
     * Resize the layer by specifying pixel
     *
     * @param integer $newWidth
     * @param integer $newHeight
     * @param boolean $converseProportion
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     * 
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     * 
     * $positionX, $positionY, $position can be ignored unless you choose a new width AND a new height AND to conserve proportion.
     */
    public function resizeInPixel($newWidth = null, $newHeight = null, $converseProportion = false, $positionX = 0, $positionY = 0, $position = 'MM')
    {
        $this->resize(self::UNIT_PIXEL, $newWidth, $newHeight, $converseProportion, $positionX, $positionY, $position);
    }

    /**
     * Resize the layer by specifying a percent
     *
     * @param float $percentWidth
     * @param float $percentHeight
     * @param boolean $converseProportion
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     * 
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     * 
     * $positionX, $positionY, $position can be ignored unless you choose a new width AND a new height AND to conserve proportion.
     */
    public function resizeInPercent($percentWidth = null, $percentHeight = null, $converseProportion = false, $positionX = 0, $positionY = 0, $position = 'MM')
    {
        $this->resize(self::UNIT_PERCENT, $percentWidth, $percentHeight, $converseProportion, $positionX, $positionY, $position);
    }
    
    /**
     * Resize the layer to fit a bounding box by specifying pixel
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $converseProportion
     */
    public function resizeToFit($width, $height, $converseProportion = false)
    {
        if ($this->getWidth() <= $width && $this->getHeight() <= $height) {
            return;
        }

        if (!$converseProportion) {
            $width = min($width, $this->getWidth());
            $height = min($height, $this->getHeight());
        }
        
        $this->resize(self::UNIT_PIXEL, $width, $height, $converseProportion ? 2 : false);
    }
    
    /**
     * Resize the layer
     *
     * @param string $unit Use one of `UNIT_*` constants, "UNIT_PIXEL" by default
     * @param mixed $newWidth (integer or float)
     * @param mixed $newHeight (integer or float)
     * @param boolean $converseProportion
     * @param mixed $positionX (integer or float)
     * @param mixed $positionY (integer or float)
     * @param string $position
     * 
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     * 
     * $positionX, $positionY, $position can be ignored unless you choose a new width AND a new height AND to conserve proportion.
     */
    public function resize($unit = self::UNIT_PIXEL, $newWidth = null, $newHeight = null, $converseProportion = false, $positionX = 0, $positionY = 0, $position = 'MM')
    {   
        if (is_numeric($newWidth) || is_numeric($newHeight)) {
            
            if ($unit == self::UNIT_PERCENT) {
                
                if ($newWidth) {
                    $newWidth = round(($newWidth / 100) * $this->width);
                }
                
                if ($newHeight) {
                    $newHeight = round(($newHeight / 100) * $this->height);
                }
            }
            
            if (is_numeric($newWidth) && $newWidth <= 0) {
                $newWidth = 1;
            }
            
            if (is_numeric($newHeight) && $newHeight <= 0) {
                $newHeight = 1;
            }
        
            if ($converseProportion) { // Proportion are conserved

                if ($newWidth && $newHeight) { // Proportions + $newWidth + $newHeight
                    
                    if ($this->getWidth() > $this->getHeight()) {
                        
                        $this->resizeInPixel($newWidth, null, true);
                        
                        if ($this->getHeight() > $newHeight) {
                            $this->resizeInPixel(null, $newHeight, true);
                        }
                        
                    } else {
                        
                        $this->resizeInPixel(null, $newHeight, true);
                        
                        if ($this->getWidth() > $newWidth) {
                            $this->resizeInPixel($newWidth, null, true);
                        }
                    }
                    
                    if ($converseProportion !== 2 && ($this->getWidth() != $newWidth || $this->getHeight() != $newHeight)) {
                        
                        $layerTmp = ImageWorkshop::initVirginLayer($newWidth, $newHeight);
                        
                        $layerTmp->addLayer(1, $this, $positionX, $positionY, $position);
                        
                        // Reset part of stack
                        
                        unset($this->image);
                        unset($this->layerLevels);
                        unset($this->layerPositions);
                        unset($this->layers);
                        
                        // Update current object
                        
                        $this->width = $layerTmp->getWidth();
                        $this->height = $layerTmp->getHeight();
                        $this->layerLevels = $layerTmp->layers[1]->getLayerLevels();
                        $this->layerPositions = $layerTmp->layers[1]->getLayerPositions();
                        $this->layers = $layerTmp->layers[1]->getLayers();
                        $this->lastLayerId = $layerTmp->layers[1]->getLastLayerId();
                        $this->highestLayerLevel = $layerTmp->layers[1]->getHighestLayerLevel();
                        
                        $translations = $layerTmp->getLayerPositions(1);
                        
                        foreach ($this->layers as $id => $layer) {
                            $this->applyTranslation($id, $translations['x'], $translations['y']);
                        }
                        
                        $layerTmp->layers[1]->clearStack(false);
                        $this->image = $layerTmp->getResult();
                        unset($layerTmp);
                    }
                    
                    return;
                    
                } elseif ($newWidth) {

                    $widthResizePercent = $newWidth / ($this->width / 100);
                    $newHeight = round(($widthResizePercent / 100) * $this->height);
                    $heightResizePercent = $widthResizePercent;

                } elseif ($newHeight) {

                    $heightResizePercent = $newHeight / ($this->height / 100);
                    $newWidth = round(($heightResizePercent / 100) * $this->width);
                    $widthResizePercent = $heightResizePercent;
                }

            } elseif (($newWidth && !$newHeight) || (!$newWidth && $newHeight)) { // New width OR new height is given

                if ($newWidth) {

                    $widthResizePercent = $newWidth / ($this->width / 100);
                    $heightResizePercent = 100;
                    $newHeight = $this->height;

                } else {

                    $heightResizePercent = $newHeight / ($this->height / 100);
                    $widthResizePercent = 100;
                    $newWidth = $this->width;
                }

            } else { // New width AND new height are given

                $widthResizePercent = $newWidth / ($this->width / 100);
                $heightResizePercent = $newHeight / ($this->height / 100);
            }

            // Update the layer positions in the stack

            foreach ($this->layerPositions as $layerId => $layerPosition) {

                $newPosX = round(($widthResizePercent / 100) * $layerPosition['x']);
                $newPosY = round(($heightResizePercent / 100) * $layerPosition['y']);

                $this->changePosition($layerId, $newPosX, $newPosY);
            }

            // Resize layers in the stack

            $layers = $this->layers;

            foreach ($layers as $key => $layer) {
                $layer->resizeInPercent($widthResizePercent, $heightResizePercent);
                $this->layers[$key] = $layer;
            }

            $this->resizeBackground($newWidth, $newHeight); // Resize the layer
        }
    }

    /**
     * Resize the layer by its largest side by specifying pixel
     *
     * @param integer $newLargestSideWidth
     * @param boolean $converseProportion
     */
    public function resizeByLargestSideInPixel($newLargestSideWidth, $converseProportion = false)
    {
        $this->resizeByLargestSide(self::UNIT_PIXEL, $newLargestSideWidth, $converseProportion);
    }

    /**
     * Resize the layer by its largest side by specifying percent
     *
     * @param integer $newLargestSideWidth percent
     * @param boolean $converseProportion
     */
    public function resizeByLargestSideInPercent($newLargestSideWidth, $converseProportion = false)
    {
        $this->resizeByLargestSide(self::UNIT_PERCENT, $newLargestSideWidth, $converseProportion);
    }

    /**
     * Resize the layer by its largest side
     *
     * @param string $unit
     * @param integer $newLargestSideWidth percent
     * @param boolean $converseProportion
     */
    public function resizeByLargestSide($unit = self::UNIT_PIXEL, $newLargestSideWidth, $converseProportion = false)
    {
        if ($unit == self::UNIT_PERCENT) {
            $newLargestSideWidth = round(($newLargestSideWidth / 100) * $this->getLargestSideWidth());
        }

        if ($this->getWidth() > $this->getHeight()) {
            $this->resizeInPixel($newLargestSideWidth, null, $converseProportion);
        } else {
            $this->resizeInPixel(null, $newLargestSideWidth, $converseProportion);
        }
    }

    /**
     * Resize the layer by its narrow side by specifying pixel
     *
     * @param integer $newNarrowSideWidth
     * @param boolean $converseProportion
     */
    public function resizeByNarrowSideInPixel($newNarrowSideWidth, $converseProportion = false)
    {
        $this->resizeByNarrowSide(self::UNIT_PIXEL, $newNarrowSideWidth, $converseProportion);
    }

    /**
     * Resize the layer by its narrow side by specifying percent
     *
     * @param integer $newNarrowSideWidth percent
     * @param boolean $converseProportion
     */
    public function resizeByNarrowSideInPercent($newNarrowSideWidth, $converseProportion = false)
    {
        $this->resizeByNarrowSide(self::UNIT_PERCENT, $newNarrowSideWidth, $converseProportion);
    }

    /**
     * Resize the layer by its narrow side
     *
     * @param string $unit
     * @param integer $newNarrowSideWidth
     * @param boolean $converseProportion
     */
    public function resizeByNarrowSide($unit = self::UNIT_PIXEL, $newNarrowSideWidth, $converseProportion = false)
    {
        if ($unit == self::UNIT_PERCENT) {
            $newNarrowSideWidth = round(($newNarrowSideWidth / 100) * $this->getNarrowSideWidth());
        }

        if ($this->getWidth() < $this->getHeight()) {
            $this->resizeInPixel($newNarrowSideWidth, null, $converseProportion);
        } else {
            $this->resizeInPixel(null, $newNarrowSideWidth, $converseProportion);
        }
    }

    /**
     * Crop the document by specifying pixels
     *
     * $backgroundColor: can be set transparent (The script will be longer to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param integer $width
     * @param integer $height
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     */
    public function cropInPixel($width = 0, $height = 0, $positionX = 0, $positionY = 0, $position = 'LT')
    {
        $this->crop(self::UNIT_PIXEL, $width, $height, $positionX, $positionY, $position);
    }

    /**
     * Crop the document by specifying percent
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param float $percentWidth
     * @param float $percentHeight
     * @param float $positionXPercent
     * @param float $positionYPercent
     * @param string $position
     */
    public function cropInPercent($percentWidth = 0, $percentHeight = 0, $positionXPercent = 0, $positionYPercent = 0, $position = 'LT')
    {
        $this->crop(self::UNIT_PERCENT, $percentWidth, $percentHeight, $positionXPercent, $positionYPercent, $position);
    }

    /**
     * Crop the document
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param string $unit
     * @param mixed $width (integer or float)
     * @param mixed $height (integer or float)
     * @param mixed $positionX (integer or float)
     * @param mixed $positionY (integer or float)
     * @param string $position
     */
    public function crop($unit = self::UNIT_PIXEL, $width = 0, $height = 0, $positionX = 0, $positionY = 0, $position = 'LT')
    {
        if ($width < 0 || $height < 0) {
            throw new ImageWorkshopLayerException('You can\'t use negative $width or $height for "'.__METHOD__.'" method.', static::ERROR_NEGATIVE_NUMBER_USED);
        }
        
        if ($unit == self::UNIT_PERCENT) {

            $width = round(($width / 100) * $this->width);
            $height = round(($height / 100) * $this->height);

            $positionX = round(($positionX / 100) * $this->width);
            $positionY = round(($positionY / 100) * $this->height);
        }
        
        if (($width != $this->width || $positionX == 0) || ($height != $this->height || $positionY == 0)) {
            
            if ($width == 0) {
                $width = 1;
            }
            
            if ($height == 0) {
                $height = 1;
            }
            
            $layerTmp = ImageWorkshop::initVirginLayer($width, $height);
            $layerClone = ImageWorkshop::initVirginLayer($this->width, $this->height);
            
            imagedestroy($layerClone->image);
            $layerClone->image = $this->image;
            
            $layerTmp->addLayer(1, $layerClone, -$positionX, -$positionY, $position);
            
            $newPos = $layerTmp->getLayerPositions();
            $layerNewPosX = $newPos[1]['x'];
            $layerNewPosY = $newPos[1]['y'];
            
            // update the layer
            $this->width = $layerTmp->getWidth();
            $this->height = $layerTmp->getHeight();
            $this->image = $layerTmp->getResult();
            unset($layerTmp);
            unset($layerClone);
            
            $this->updateLayerPositionsAfterCropping($layerNewPosX, $layerNewPosY);
        }
    }
    
    /**
     * Crop the document to a specific aspect ratio by specifying a shift in pixel
     *
     * $backgroundColor: can be set transparent (The script will be longer to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param integer $width
     * @param integer $height
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     */
    public function cropToAspectRatioInPixel($width = 0, $height = 0, $positionX = 0, $positionY = 0, $position = 'LT')
    {
        $this->cropToAspectRatio(self::UNIT_PIXEL, $width, $height, $positionX, $positionY, $position);
    }

    /**
     * Crop the document to a specific aspect ratio by specifying a shift in percent
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param integer $width
     * @param integer $height
     * @param float $positionXPercent
     * @param float $positionYPercent
     * @param string $position
     */
    public function cropToAspectRatioInPercent($width = 0, $height = 0, $positionXPercent = 0, $positionYPercent = 0, $position = 'LT')
    {
        $this->cropToAspectRatio(self::UNIT_PERCENT, $width, $height, $positionXPercent, $positionYPercent, $position);
    }

    /**
     * Crop the document to a specific aspect ratio
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param string $unit
     * @param integer $width (integer or float)
     * @param integer $height (integer or float)
     * @param mixed $positionX (integer or float)
     * @param mixed $positionY (integer or float)
     * @param string $position
     */
    public function cropToAspectRatio($unit = self::UNIT_PIXEL, $width = 0, $height = 0, $positionX = 0, $positionY = 0, $position = 'LT')
    {
        if ($width < 0 || $height < 0) {
            throw new ImageWorkshopLayerException('You can\'t use negative $width or $height for "'.__METHOD__.'" method.', static::ERROR_NEGATIVE_NUMBER_USED);
        }
        
        if ($width == 0) {
            $width = 1;
        }

        if ($height == 0) {
            $height = 1;
        }

        if ($this->width / $this->height <= $width / $height) {
            $newWidth = $this->width;
            $newHeight = round($height * ($this->width / $width));
        } else {
            $newWidth = round($width * ($this->height / $height));
            $newHeight = $this->height;
        }
        
        if ($unit == self::UNIT_PERCENT) {
            $positionX = round(($positionX / 100) * ($this->width - $newWidth));
            $positionY = round(($positionY / 100) * ($this->height - $newHeight));
        }

        $this->cropInPixel($newWidth, $newHeight, $positionX, $positionY, $position);
    }

    /**
     * Crop the maximum possible from left top ("LT"), "RT"... by specifying a shift in pixel
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param integer $width
     * @param integer $height
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     */
    public function cropMaximumInPixel($positionX = 0, $positionY = 0, $position = 'LT')
    {
        $this->cropMaximum(self::UNIT_PIXEL, $positionX, $positionY, $position);
    }

    /**
     * Crop the maximum possible from left top ("LT"), "RT"... by specifying a shift in percent
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param integer $width
     * @param integer $height
     * @param integer $positionXPercent
     * @param integer $positionYPercent
     * @param string $position
     */
    public function cropMaximumInPercent($positionXPercent = 0, $positionYPercent = 0, $position = 'LT')
    {
        $this->cropMaximum(self::UNIT_PERCENT, $positionXPercent, $positionYPercent, $position);
    }

    /**
     * Crop the maximum possible from left top
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param string $unit
     * @param integer $width
     * @param integer $height
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     */
    public function cropMaximum($unit = self::UNIT_PIXEL, $positionX = 0, $positionY = 0, $position = 'LT')
    {
        $narrowSide = $this->getNarrowSideWidth();
        
        if ($unit == self::UNIT_PERCENT) {
            $positionX = round(($positionX / 100) * $this->width);
            $positionY = round(($positionY / 100) * $this->height);
        }

        $this->cropInPixel($narrowSide, $narrowSide, $positionX, $positionY, $position);
    }
    
    /**
     * Rotate the layer (in degree)
     *
     * @param float $degrees
     */
    public function rotate($degrees)
    {
        if ($degrees != 0) {

            if ($degrees < -360 || $degrees > 360) {
                $degrees = $degrees % 360;
            }

            if ($degrees < 0 && $degrees >= -360) {
                $degrees = 360 + $degrees;
            }

            $transparentColor = imageColorAllocateAlpha($this->image, 0, 0, 0, 127);
            $rotationDegrees = ($degrees > 0) ? intval(360 * (1 - $degrees / 360)) : $degrees; // Used to fixed PHP >= 5.5 rotation with base angle 90°, 180°

            // Rotate the layer background image
            $imageRotated = imagerotate($this->image, $rotationDegrees, $transparentColor);
            imagealphablending($imageRotated, true);
            imagesavealpha($imageRotated, true);

            unset($this->image);

            $this->image = $imageRotated;

            $oldWidth = $this->width;
            $oldHeight = $this->height;

            $this->width = imagesx($this->image);
            $this->height = imagesy($this->image);

            foreach ($this->layers as $layerId => $layer) {

                $layerSelfOldCenterPosition = array(
                    'x' => $layer->width / 2,
                    'y' => $layer->height / 2,
                );

                $smallImageCenter = array(
                    'x' => $layerSelfOldCenterPosition['x'] + $this->layerPositions[$layerId]['x'],
                    'y' => $layerSelfOldCenterPosition['y'] + $this->layerPositions[$layerId]['y'],
                );

                $this->layers[$layerId]->rotate($degrees);

                $ro = sqrt(pow($smallImageCenter['x'], 2) + pow($smallImageCenter['y'], 2));

                $teta = (acos($smallImageCenter['x'] / $ro)) * 180 / pi();

                $a = $ro * cos(($teta + $degrees) * pi() / 180);
                $b = $ro * sin(($teta + $degrees) * pi() / 180);

                if ($degrees > 0 && $degrees <= 90) {

                    $newPositionX = $a - ($this->layers[$layerId]->width / 2) + $oldHeight * sin(($degrees * pi()) / 180);
                    $newPositionY = $b - ($this->layers[$layerId]->height / 2);

                } elseif ($degrees > 90 && $degrees <= 180) {

                    $newPositionX = $a - ($this->layers[$layerId]->width / 2) + $this->width;
                    $newPositionY = $b - ($this->layers[$layerId]->height / 2) + $oldHeight * (-cos(($degrees) * pi() / 180));

                } elseif ($degrees > 180 && $degrees <= 270) {

                    $newPositionX = $a - ($this->layers[$layerId]->width / 2) + $oldWidth * (-cos(($degrees) * pi() / 180));
                    $newPositionY = $b - ($this->layers[$layerId]->height / 2) + $this->height;

                } else {

                    $newPositionX = $a - ($this->layers[$layerId]->width / 2);
                    $newPositionY = $b - ($this->layers[$layerId]->height / 2) + $oldWidth * (-sin(($degrees) * pi() / 180));
                }

                $this->layerPositions[$layerId] = array(
                    'x' => $newPositionX,
                    'y' => $newPositionY,
                );
            }
        }
    }

    /**
     * Change the opacity of the layer
     * $recursive: apply it on sublayers
     *
     * @param integer $opacity
     * @param boolean $recursive
     */
    public function opacity($opacity, $recursive = true)
    {
        if ($recursive) {

            $layers = $this->layers;

            foreach ($layers as $key => $layer) {
                $layer->opacity($opacity, true);
                $this->layers[$key] = $layer;
            }
        }

        $transparentImage = ImageWorkshopLib::generateImage($this->getWidth(), $this->getHeight());
        
        ImageWorkshopLib::imageCopyMergeAlpha($transparentImage, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $opacity);

        unset($this->image);
        $this->image = $transparentImage;
        unset($transparentImage);
    }
    
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
    public function applyFilter($filterType, $arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $recursive = false)
    {
        if ($filterType == IMG_FILTER_COLORIZE) {
            imagefilter($this->image, $filterType, $arg1, $arg2, $arg3, $arg4);
        } elseif ($filterType == IMG_FILTER_BRIGHTNESS || $filterType == IMG_FILTER_CONTRAST || $filterType == IMG_FILTER_SMOOTH) {
            imagefilter($this->image, $filterType, $arg1);
        } elseif ($filterType == IMG_FILTER_PIXELATE) {
            imagefilter($this->image, $filterType, $arg1, $arg2);
        } else {
            imagefilter($this->image, $filterType);
        }

        if ($recursive) {

            $layers = $this->layers;

            foreach($layers as $layerId => $layer) {
                $this->layers[$layerId]->applyFilter($filterType, $arg1, $arg2, $arg3, $arg4, true);
            }
        }
    }
    
    /**
     * Apply horizontal or vertical flip (Transformation)
     * 
     * @param string $type
     */
    public function flip($type = 'horizontal')
    {
        $layers = $this->layers;

        foreach ($layers as $key => $layer) {

            $layer->flip($type);
            $this->layers[$key] = $layer;
        }
        
        $temp = ImageWorkshopLib::generateImage($this->width, $this->height);
        
        if ($type == 'horizontal') {
            
            imagecopyresampled($temp, $this->image, 0, 0, $this->width - 1, 0, $this->width, $this->height, -$this->width, $this->height);
            $this->image = $temp;
            
            foreach ($this->layerPositions as $layerId => $layerPositions) {
    
                $this->changePosition($layerId, $this->width - $this->layers[$layerId]->getWidth() - $layerPositions['x'], $layerPositions['y']);
            }
            
        } elseif ($type == 'vertical') {
            
            imagecopyresampled($temp, $this->image, 0, 0, 0, $this->height - 1, $this->width, $this->height, $this->width, -$this->height);
            $this->image = $temp;
            
            foreach ($this->layerPositions as $layerId => $layerPositions) {
    
                $this->changePosition($layerId, $layerPositions['x'], $this->height - $this->layers[$layerId]->getHeight() - $layerPositions['y']);
            }
        }
        
        unset($temp);
    }
    
    /**
     * Add a text on the background image of the layer using a default font registered in GD
     *
     * @param string $text
     * @param integer $font
     * @param string $color
     * @param integer $positionX
     * @param integer $positionY
     * @param string $align
     */
    public function writeText($text, $font = 1, $color = 'ffffff', $positionX = 0, $positionY = 0, $align = 'horizontal')
    {
        $RGBTextColor = ImageWorkshopLib::convertHexToRGB($color);
        $textColor = imagecolorallocate($this->image, $RGBTextColor['R'], $RGBTextColor['G'], $RGBTextColor['B']);

        if ($align == 'horizontal') {
            imagestring($this->image, $font, $positionX, $positionY, $text, $textColor);
        } else {
            imagestringup($this->image, $font, $positionX, $positionY, $text, $textColor);
        }
    }

    /**
     * Add a text on the background image of the layer using a font localized at $fontPath
     * Return the text coordonates
     *
     * @param string $text
     * @param integer $fontPath
     * @param integer $fontSize
     * @param string $color
     * @param integer $positionX
     * @param integer $positionY
     * @param integer $fontRotation
     *
     * @return array
     */
    public function write($text, $fontPath, $fontSize = 13, $color = 'ffffff', $positionX = 0, $positionY = 0, $fontRotation = 0)
    {
        if (!file_exists($fontPath)) {
            throw new ImageWorkshopLayerException('Can\'t find a font file at this path : "'.$fontPath.'".', static::ERROR_FONT_NOT_FOUND);
        }
        
        $RGBTextColor = ImageWorkshopLib::convertHexToRGB($color);
        $textColor = imagecolorallocate($this->image, $RGBTextColor['R'], $RGBTextColor['G'], $RGBTextColor['B']);

        return imagettftext($this->image, $fontSize, $fontRotation, $positionX, $positionY, $textColor, $fontPath, $text);
    }
    
    // Manage the result
    // =========================================================
    
    /**
     * Return a merged resource image
     *
     * $backgroundColor is really usefull if you want to save a JPG or GIF, because the transparency of the background
     * would be remove for a colored background, so you should choose a color like "ffffff" (white)
     *
     * @param string $backgroundColor
     *
     * @return resource
     */
    public function getResult($backgroundColor = null)
    {
        $imagesToMerge = array();
        ksort($this->layerLevels);

        foreach ($this->layerLevels as $layerLevel => $layerId) {

            $imagesToMerge[$layerLevel] = $this->layers[$layerId]->getResult();

            // Layer positions
            if ($this->layerPositions[$layerId]['x'] != 0 || $this->layerPositions[$layerId]['y'] != 0) {
                
                $virginLayoutImageTmp = ImageWorkshopLib::generateImage($this->width, $this->height);
                ImageWorkshopLib::mergeTwoImages($virginLayoutImageTmp, $imagesToMerge[$layerLevel], $this->layerPositions[$layerId]['x'], $this->layerPositions[$layerId]['y'], 0, 0);
                $imagesToMerge[$layerLevel] = $virginLayoutImageTmp;
                unset($virginLayoutImageTmp);
            }
        }

        $iterator = 1;
        $mergedImage = $this->image;
        ksort($imagesToMerge);

        foreach ($imagesToMerge as $imageLevel => $image) {
            ImageWorkshopLib::mergeTwoImages($mergedImage, $image);
            $iterator++;
        }
        
        $opacity = 127;
        
        if ($backgroundColor && $backgroundColor != 'transparent') {
            $opacity = 0;
        }
        
        $backgroundImage = ImageWorkshopLib::generateImage($this->width, $this->height, $backgroundColor, $opacity);
        ImageWorkshopLib::mergeTwoImages($backgroundImage, $mergedImage);
        $mergedImage = $backgroundImage;
        unset($backgroundImage);

        return $mergedImage;
    }

    /**
     * Save the resulting image at the specified path
     *
     * $backgroundColor is really usefull if you want to save a JPG or GIF, because the transparency of the background
     * would be remove for a colored background, so you should choose a color like "ffffff" (white)
     *
     * If the file already exists, it will be override !
     *
     * $imageQuality is useless for GIF
     *
     * Ex: $folder = __DIR__."/../web/images/2012"
     *     $imageName = "butterfly.jpg"
     *     $createFolders = true
     *     $imageQuality = 95
     *     $backgroundColor = "ffffff"
     *
     * @param string $folder
     * @param string $imageName
     * @param boolean $createFolders
     * @param string $backgroundColor
     * @param integer $imageQuality
     * @param boolean $interlace
     */
    public function save($folder, $imageName, $createFolders = true, $backgroundColor = null, $imageQuality = 75, $interlace = false)
    {
        if (is_file($folder)) {
            throw new ImageWorkshopLayerException(sprintf('Destination folder "%s" is a file.', $folder), self::ERROR_NOT_WRITABLE_FOLDER);
        }

        if ((!is_dir($folder) && !$createFolders)) {
            throw new ImageWorkshopLayerException(sprintf('Destination folder "%s" not exists.', $folder), self::ERROR_NOT_WRITABLE_FOLDER);
        }

        if (is_dir($folder) && !is_writable($folder)) {
            throw new ImageWorkshopLayerException(sprintf('Destination folder "%s" not writable.', $folder), self::ERROR_NOT_WRITABLE_FOLDER);
        }

        // Creating the folders if they don't exist
        if (!is_dir($folder) && $createFolders) {
            if (!mkdir($folder, 0777, true)) {
                throw new ImageWorkshopLayerException(sprintf('Unable to create destination folder "%s".', $folder), self::ERROR_NOT_WRITABLE_FOLDER);
            }

            $oldUmask = umask(0);
            umask($oldUmask);
            chmod($folder, 0777);
        }

        $extension = explode('.', $imageName);
        $extension = strtolower($extension[count($extension) - 1]);

        $filename = $folder.'/'.$imageName;

        if (($extension == 'jpg' || $extension == 'jpeg' || $extension == 'gif') && (!$backgroundColor || $backgroundColor == 'transparent')) {
            $backgroundColor = 'ffffff';
        }

        $image = $this->getResult($backgroundColor);

        imageinterlace($image, (int) $interlace);

        if ($extension == 'jpg' || $extension == 'jpeg') {

            $isSaved = imagejpeg($image, $filename, $imageQuality);

        } elseif ($extension == 'gif') {

            $isSaved = imagegif($image, $filename);

        } elseif ($extension == 'png') {

            $imageQuality = $imageQuality / 10;
            $imageQuality -= 1;

            $isSaved = imagepng($image, $filename, intval($imageQuality));

        } else {

            throw new ImageWorkshopLayerException(sprintf('Image format "%s" not supported.', $extension), self::ERROR_NOT_SUPPORTED_FORMAT);

        }

        if (!$isSaved) {
            throw new ImageWorkshopLayerException(sprintf('Error occurs when save image "%s".', $folder), self::ERROR_UNKNOW);
        }

        unset($image);
    }

    // Checkers
    // =========================================================

    /**
     * Check if a sublayer exists in the stack for a given id
     *
     * @param integer $layerId
     *
     * @return boolean
     */
    public function isLayerInIndex($layerId)
    {
        if (array_key_exists($layerId, $this->layers)) {
            return true;
        }

        return false;
    }

    // Getter / Setter
    // =========================================================
    
    /**
     * Return the narrow side width of the layer
     *
     * @return integer
     */
    public function getNarrowSideWidth()
    {
        $narrowSideWidth = $this->getWidth();

        if ($this->getHeight() < $narrowSideWidth) {
            $narrowSideWidth = $this->getHeight();
        }

        return $narrowSideWidth;
    }

    /**
     * Return the largest side width of the layer
     *
     * @return integer
     */
    public function getLargestSideWidth()
    {
        $largestSideWidth = $this->getWidth();

        if ($this->getHeight() > $largestSideWidth) {
            $largestSideWidth = $this->getHeight();
        }

        return $largestSideWidth;
    }
    
    /**
     * Get the level of a sublayer
     * Return sublayer level if success or false if layer isn't found
     *
     * @param integer $layerId
     *
     * @return mixed (integer or boolean)
     */
    public function getLayerLevel($layerId)
    {
        if ($this->isLayerInIndex($layerId)) { // if the layer exists in document
            return array_search($layerId, $this->layerLevels);
        }

        return false;
    }
    
    /**
     * Get a sublayer in the stack
     * Don't forget to use clone method: $b = clone $a->getLayer(3);
     *
     * @param integer $layerId
     *
     * @return ImageWorkshop
     */
    public function getLayer($layerId)
    {
        return $this->layers[$layerId];
    }
    
    /**
     * Getter width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Getter height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Getter image
     *
     * @return resource
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Getter layers
     *
     * @return array
     */
    public function getLayers()
    {
        return $this->layers;
    }

    /**
     * Getter layerLevels
     *
     * @return array
     */
    public function getLayerLevels()
    {
        return $this->layerLevels;
    }

    /**
     * Getter layerPositions
     * 
     * Get all the positions of the sublayers,
     * or when specifying $layerId, get the position of this sublayer
     *
     * @param integer $layerId
     * 
     * @return mixed (array or boolean)
     */
    public function getLayerPositions($layerId = null)
    {
        if (!$layerId) {
            
            return $this->layerPositions;
            
        } elseif ($this->isLayerInIndex($layerId)) { // if the sublayer exists in the stack
            
            return $this->layerPositions[$layerId];
        }
        
        return false;
    }

    /**
     * Getter highestLayerLevel
     *
     * @return array
     */
    public function getHighestLayerLevel()
    {
        return $this->highestLayerLevel;
    }

    /**
     * Getter lastLayerId
     *
     * @return array
     */
    public function getLastLayerId()
    {
        return $this->lastLayerId;
    }
    
    // Internals
    // =========================================================
    
    /**
     * Delete the current object
     */
    public function delete()
    {
        imagedestroy($this->image);
        $this->clearStack();
    }
    
    /**
     * Create a new background image var from the old background image var
     */
    public function createNewVarFromBackgroundImage()
    {
        $virginImage = ImageWorkshopLib::generateImage($this->getWidth(), $this->getHeight()); // New background image
        
        ImageWorkshopLib::mergeTwoImages($virginImage, $this->image, 0, 0, 0, 0);
        unset($this->image);

        $this->image = $virginImage;
        unset($virginImage);

        $layers = $this->layers;

        foreach($layers as $layerId => $layer) {
            $this->layers[$layerId] = clone $this->layers[$layerId];
        }
    }
    
    /**
     * Index a sublayer in the layer stack
     * Return an array containing the generated sublayer id and its final level:
     * array("layerLevel" => integer, "id" => integer)
     *
     * @param integer $layerLevel
     * @param ImageWorkshopLayer $layer
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     *
     * @return array
     */
    protected function indexLayer($layerLevel, $layer, $positionX = 0, $positionY = 0, $position)
    {
        // Choose an id for the added layer
        $layerId = $this->lastLayerId + 1;

        // Clone $layer to duplicate image resource var
        $layer = clone $layer;

        // Add the layer in the stack
        $this->layers[$layerId] = $layer;

        // Add the layer positions in the main layer
        $this->layerPositions[$layerId] = ImageWorkshopLib::calculatePositions($this->getWidth(), $this->getHeight(), $layer->getWidth(), $layer->getHeight(), $positionX, $positionY, $position);

        // Update the lastLayerId of the workshop
        $this->lastLayerId = $layerId;

        // Add the layer level in the stack
        $layerLevel = $this->indexLevelInDocument($layerLevel, $layerId);

        return array(
            'layerLevel' => $layerLevel,
            'id' => $layerId,
        );
    }

    /**
     * Index a layer level and update the layers levels in the document
     * Return the corrected level of the layer
     *
     * @param integer $layerLevel
     * @param integer $layerId
     *
     * @return integer
     */
    protected function indexLevelInDocument($layerLevel, $layerId)
    {
        if (array_key_exists($layerLevel, $this->layerLevels)) { // Level already exists
 
            ksort($this->layerLevels); // All layers after this level and the layer which have this level are updated
            $layerLevelsTmp = $this->layerLevels;

            foreach ($layerLevelsTmp as $levelTmp => $layerIdTmp) {
                if ($levelTmp >= $layerLevel) {
                    $this->layerLevels[$levelTmp + 1] = $layerIdTmp;
                }
            }

            unset($layerLevelsTmp);

        } else { // Level isn't taken
            if ($this->highestLayerLevel < $layerLevel) { // If given level is too high, proceed adjustement
                $layerLevel = $this->highestLayerLevel + 1;
            }
        }

        $this->layerLevels[$layerLevel] = $layerId;
        $this->highestLayerLevel = max(array_flip($this->layerLevels)); // Update $highestLayerLevel

        return $layerLevel;
    }
    
    /**
     * Update the positions of layers in the stack after cropping
     *
     * @param integer $positionX
     * @param integer $positionY
     */
    public function updateLayerPositionsAfterCropping($positionX, $positionY)
    {
        foreach ($this->layers as $layerId => $layer) {

            $oldLayerPosX = $this->layerPositions[$layerId]['x'];
            $oldLayerPosY = $this->layerPositions[$layerId]['y'];

            $newLayerPosX = $oldLayerPosX + $positionX;
            $newLayerPosY = $oldLayerPosY + $positionY;

            $this->changePosition($layerId, $newLayerPosX, $newLayerPosY);
        }
    }
    
    /**
     * Resize the background of a layer
     *
     * @param integer $newWidth
     * @param integer $newHeight
     */
    public function resizeBackground($newWidth, $newHeight)
    {
        $oldWidth = $this->width;
        $oldHeight = $this->height;

        $this->width = $newWidth;
        $this->height = $newHeight;

        $virginLayoutImage = ImageWorkshopLib::generateImage($this->width, $this->height);

        imagecopyresampled($virginLayoutImage, $this->image, 0, 0, 0, 0, $this->width, $this->height, $oldWidth, $oldHeight);

        unset($this->image);
        $this->image = $virginLayoutImage;
    }

    /**
     * Fix image orientation based on exif data
     */
    public function fixOrientation()
    {
        if (!isset($this->exif['Orientation']) || 0 == $this->exif['Orientation']) {
            return;
        }

        switch ($this->exif['Orientation']) {
            case ExifOrientations::TOP_RIGHT:
                $this->flip('horizontal');
            break;

            case ExifOrientations::BOTTOM_RIGHT:
                $this->rotate(180);
            break;

            case ExifOrientations::BOTTOM_LEFT:
                $this->flip('vertical');
            break;

            case ExifOrientations::LEFT_TOP:
                $this->rotate(-90);
                $this->flip('vertical');
            break;

            case ExifOrientations::RIGHT_TOP:
                $this->rotate(90);
            break;

            case ExifOrientations::RIGHT_BOTTOM:
                $this->rotate(90);
                $this->flip('horizontal');
            break;

            case ExifOrientations::LEFT_BOTTOM:
                $this->rotate(-90);
            break;
        }

        $this->exif['Orientation'] = ExifOrientations::TOP_LEFT;
    }
    
    // Deprecated, don't use anymore
    // =========================================================
    
    /**
	 * @deprecated
     */
    public function resizeInPourcent($percentWidth = null, $percentHeight = null, $converseProportion = false, $positionX = 0, $positionY = 0, $position = 'MM')
    {
		throw new ImageWorkshopLayerException('Method resizeInPourcent() was renamed resizeInPercent(). Use resizeInPercent() instead.', static::METHOD_DEPRECATED);
    }
	
	/**
     * @deprecated
     */
    public function resizeByLargestSideInPourcent($newLargestSideWidth, $converseProportion = false)
    {
        throw new ImageWorkshopLayerException('Method resizeByLargestSideInPourcent() was renamed resizeByLargestSideInPercent(). Use resizeByLargestSideInPercent() instead.', static::METHOD_DEPRECATED);
    }
	
	/**
     * @deprecated
     */
    public function resizeByNarrowSideInPourcent($newNarrowSideWidth, $converseProportion = false)
    {
        throw new ImageWorkshopLayerException('Method resizeByNarrowSideInPourcent() was renamed resizeByNarrowSideInPercent(). Use resizeByNarrowSideInPercent() instead.', static::METHOD_DEPRECATED);
    }
	
	/**
     * @deprecated
     */
    public function cropInPourcent($percentWidth = 0, $percentHeight = 0, $positionXPercent = 0, $positionYPercent = 0, $position = 'LT')
    {
		throw new ImageWorkshopLayerException('Method cropInPourcent() was renamed cropInPercent(). Use cropInPercent() instead.', static::METHOD_DEPRECATED);
	}
	
	/**
     * @deprecated
     */
    public function cropMaximumInPourcent($positionXPercent = 0, $positionYPercent = 0, $position = 'LT')
    {
        throw new ImageWorkshopLayerException('Method cropMaximumInPourcent() was renamed cropMaximumInPercent(). Use cropMaximumInPercent() instead.', static::METHOD_DEPRECATED);
    }
}

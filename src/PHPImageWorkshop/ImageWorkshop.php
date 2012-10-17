<?php

namespace PHPImageWorkshop;

/**
 * ImageWorkshop class
 *
 * Powerful PHP class using GD library to work easily with images including layer notion (like Photoshop or GIMP).
 * ImageWorkshop can be used as a layer, a group or a document.
 *
 * @version 1.3.1
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain  / @Sybio01)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Clément Guillemain
 */
class ImageWorkshop
{
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

    // Methods
    // ===================================================================================

    /**
     * Constructor
     *
     * @param array $params
     */
    public function __construct($params = array())
    {
        if (!extension_loaded('gd')) {
            throw new \Exception('PHPImageWorkshop requires the GD extension to be loaded.');
        }
    	
        $this->width = 800;
        $this->height = 600;
        $this->layers = array();
        $this->layerLevels = array();
        $this->layerPositions = array();
        $imageFromPath = null;
        $imageVar = null;
        $imageString = null;
        $backgroundColor = null;
        $text = null;
        $fontPath = null;
        $fontSize = 13;
        $fontColor = 'ffffff';
        $textRotation = 0;
        $fileObject = null;
        $tmpName = null;
        $mimeType = null;

        if (array_key_exists('width', $params)) {
            $this->width = $params['width'];
        }

        if (array_key_exists('height', $params)) {
            $this->height = $params['height'];
        }

        if (array_key_exists('imageFromPath', $params)) {
            $imageFromPath = $params['imageFromPath'];
        }

        if (array_key_exists('imageVar', $params)) {
            $imageVar = $params['imageVar'];
        }
        
        if (array_key_exists('imageFromString', $params)) {
            $imageString = $params['imageFromString'];
        }

        if (array_key_exists('backgroundColor', $params)) {
            $backgroundColor = $params['backgroundColor'];
        }

        // Text layer

        if (array_key_exists('text', $params)) {
            $text = $params['text'];
        }

        if (array_key_exists('fontPath', $params)) {
            $fontPath = $params["fontPath"];
        }

        if (array_key_exists('fontSize', $params)) {
            $fontSize = $params['fontSize'];
        }

        if (array_key_exists('fontColor', $params)) {
            $fontColor = $params['fontColor'];
        }

        if (array_key_exists('textRotation', $params)) {
            $textRotation = $params['textRotation'];
        }

        // Uploaded file object layer

        if (array_key_exists('fileObject', $params)) {

            $fileObject = $params['fileObject'];

        } elseif (array_key_exists('tmpName', $params)) {

            $tmpName = $params['tmpName'];
        }

        $this->clearStack();

        // Initialization of the layer dimensions and background image

        if ($imageFromPath || $fileObject || $tmpName) {

            if ($fileObject) {

                $imageFromPath = $fileObject['tmp_name'];

            } elseif ($tmpName) {

                $imageFromPath = $tmpName;
            }

            $this->initializeImageFrom($imageFromPath);

        } elseif ($imageVar) {

            $this->initializeImageWith($imageVar);
            
        } elseif ($imageString) {

            $imageVar = imagecreatefromstring($imageString);
    
            $this->initializeImageWith($imageVar);
            
        } elseif ($text && $fontPath) {

            $this->initializeTextImage($text, $fontPath, $fontSize, $fontColor, $textRotation, $backgroundColor);

        } else {

            $this->initializeImage($backgroundColor);
        }
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

    /**
     * Paste an image on the layer
     * You can specify the position left (in pixels) and the position top (in pixels) of the added image relatives to the layer
     * Otherwise, it will be set at 0 and 0
     *
     * @param string $unit
     * @param resource $image
     * @param integer $positionX
     * @param integer $positionY
     */
    public function pasteImage($unit = "pixel", $image, $positionX = 0, $positionY = 0)
    {
        if ($unit == "pourcent") {

            $positionX = round(($positionX / 100) * $this->width);
            $positionY = round(($positionY / 100) * $this->height);
        }

        imagecopy($this->image, $image, $positionX, $positionY, 0, 0, $image->getWidth(), $image->getHeight());
    }

    /**
     * Add an existing ImageWorkshop sublayer and set it in the stack at a given level
     * Return an array containing the generated sublayer id in the stack and its corrected level:
     * array("layerLevel" => integer, "id" => integer)
     *
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param integer $layerLevel
     * @param ImageWorkshop $layer
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     *
     * @return array
     */
    public function addLayer($layerLevel, $layer, $positionX = 0, $positionY = 0, $position = "LT")
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
     * @param ImageWorkshop $layer
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     *
     * @return array
     */
    public function addLayerOnTop($layer, $positionX = 0, $positionY = 0, $position = "LT")
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
     * @param ImageWorkshop $layer
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     *
     * @return array
     */
    public function addLayerBelow($layer, $positionX = 0, $positionY = 0, $position = "LT")
    {
        return $this->indexLayer(1, $layer, $positionX, $positionY, $position);
    }

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
            $layerPositionX = $this->layerPositions[$layerId]["x"];
            $layerPositionY = $this->layerPositions[$layerId]["y"];

            if ($layerLevel > 1) {

                $underLayerId = $this->layerLevels[$layerLevel - 1];
                $underLayer = $this->getLayer($underLayerId);
                $underLayerWidth = $underLayer->getWidth();
                $underLayerHeight = $underLayer->getHeight();
                $underLayerPositionX = $this->layerPositions[$underLayerId]["x"];
                $underLayerPositionY = $this->layerPositions[$underLayerId]["y"];

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

                $layerTmp = new static(array(
                    "width" => $layerTmpWidth,
                    "height" => $layerTmpHeight,
                ));

                $layerTmp->addLayer(1, $underLayer, $underLayerPositionX - $minLayerPositionX, $underLayerPositionY - $minLayerPositionY);
                $layerTmp->addLayer(2, $layer, $layerPositionX - $minLayerPositionX, $layerPositionY - $minLayerPositionY);

                // Update layers
                $layerTmp->mergeAll();

                $this->layers[$underLayerId] = clone $layerTmp;
                $this->changePosition($underLayerId, $minLayerPositionX, $minLayerPositionX);

            } else {

                $layerTmp = new static(array(
                    "imageVar" => $this->image,
                ));

                $layerTmp->addLayer(1, $layer, $layerPositionX, $layerPositionY);

                // Update background image
                $this->image = $layerTmp->getResult();
            }

            unset($layerTmp);

            // Remove the merged layer from the stack
            $this->remove($layerId);

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

                $isUnderAndNewLevelHigher = false;
                $isUnderAndNewLevelLower = false;
                $isOnTopAndNewLevelHigher = false;
                $isOnTopAndNewLevelLower = false;

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
        // if the sublayer exists in the stack
        if ($this->isLayerInIndex($layerId)) {

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
        // if the sublayer exists in the stack
        if ($this->isLayerInIndex($layerId)) {

            $layerOldLevel = $this->getLayerLevel($layerId);

            return $this->moveTo($layerId, $layerOldLevel - 1, true);
        }

        return false;
    }

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
     * Get the level of a sublayer
     * Return sublayer level if success or false if layer isn't found
     *
     * @param integer $layerId
     *
     * @return mixed
     */
    public function getLayerLevel($layerId)
    {
        // if the layer exists in document
        if ($this->isLayerInIndex($layerId)) {
            return array_search($layerId, $this->layerLevels);
        }

        return false;
    }

    /**
     * Check if a sublayer exists in the stack for a given id
     *
     * @param integer $layerId
     *
     * @return boolean
     */
    public function isLayerInIndex($layerId)
    {
        // if the layer exists in document
        if (array_key_exists($layerId, $this->layers)) {
            return true;
        }

        return false;
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
    public static function generateImage($width = 100, $height = 100, $color = "ffffff", $opacity = 127)
    {
        $RGBColors = static::convertHexToRGB($color);

        $image = imagecreatetruecolor($width, $height);
        imagesavealpha($image, true);
        $color = imagecolorallocatealpha($image, $RGBColors["R"], $RGBColors["G"], $RGBColors["B"], $opacity);
        imagefill($image, 0, 0, $color);

        return $image;
    }

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
        $this->resize('pixel', $newWidth, $newHeight, $converseProportion, $positionX, $positionY, $position);
    }

    /**
     * Resize the layer by specifying a pourcent
     *
     * @param float $pourcentWidth
     * @param float $pourcentHeight
     * @param boolean $converseProportion
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     * 
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     * 
     * $positionX, $positionY, $position can be ignored unless you choose a new width AND a new height AND to conserve proportion.
     */
    public function resizeInPourcent($pourcentWidth = null, $pourcentHeight = null, $converseProportion = false, $positionX = 0, $positionY = 0, $position = 'MM')
    {
        $this->resize('pourcent', $pourcentWidth, $pourcentHeight, $converseProportion, $positionX, $positionY, $position);
    }
    
    /**
     * Resize the layer
     *
     * @param string $unit
     * @param float $pourcentWidth
     * @param float $pourcentHeight
     * @param boolean $converseProportion
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     * 
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     * 
     * $positionX, $positionY, $position can be ignored unless you choose a new width AND a new height AND to conserve proportion.
     */
    public function resize($unit = "pixel", $newWidth = null, $newHeight = null, $converseProportion = false, $positionX = 0, $positionY = 0, $position = 'MM')
    {
        if ($newWidth || $newHeight) {
            
            if ($unit == 'pourcent') {
                
                if ($newWidth) {
                    
                    $newWidth = round(($newWidth / 100) * $this->width);
                }
                
                if ($newHeight) {
                    
                    $newHeight = round(($newHeight / 100) * $this->height);
                }
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
                    
                    if ($this->getWidth() != $newWidth || $this->getHeight() != $newHeight) {
                        
                        $layerTmp = new static(array(
                            'width' => $newWidth,
                            'height' => $newHeight,
                        ));
                        
                        $layerTmp->addLayer(1, $this, round($positionX * ($newWidth / 100)), round($positionY * ($newHeight / 100)), $position);
                        
                        $this->width = $layerTmp->getWidth();
                        $this->height = $layerTmp->getHeight();
                        unset($this->image);
                        unset($this->layerLevels);
                        unset($this->layerPositions);
                        unset($this->layers);
                        $this->image = $layerTmp->getImage();
                        $this->layerLevels = $layerTmp->getLayerLevels();
                        $this->layerPositions = $layerTmp->getLayerPositions();
                        $this->layers = $layerTmp->getLayers();
                        unset($layerTmp);
                    }
                    
                    return;
                    
                } elseif ($newWidth) {

                    $widthResizePourcent = $newWidth / ($this->width / 100);

                    $newHeight = round(($widthResizePourcent / 100) * $this->height);
                    $heightResizePourcent = $widthResizePourcent;

                } elseif ($newHeight) {

                    $heightResizePourcent = $newHeight / ($this->height / 100);

                    $newWidth = round(($heightResizePourcent / 100) * $this->width);
                    $widthResizePourcent = $heightResizePourcent;
                }

            } elseif (($newWidth && !$newHeight) || (!$newWidth && $newHeight)) { // New width OR new height is given

                if ($newWidth) {

                    $widthResizePourcent = $newWidth / ($this->width / 100);

                    $heightResizePourcent = 100;
                    $newHeight = $this->height;

                } else {

                    $heightResizePourcent = $newHeight / ($this->height / 100);

                    $widthResizePourcent = 100;
                    $newWidth = $this->width;
                }

            } else { // New width AND new height are given

                $widthResizePourcent = $newWidth / ($this->width / 100);

                $heightResizePourcent = $newHeight / ($this->height / 100);
            }

            // Update the layer positions in the stack

            foreach ($this->layerPositions as $layerId => $layerPosition) {

                $newPosX = round(($widthResizePourcent / 100) * $layerPosition['x']);
                $newPosY = round(($heightResizePourcent / 100) * $layerPosition['y']);

                $this->changePosition($layerId, $newPosX, $newPosY);
            }

            // Resize layers in the stack

            $layers = $this->layers;

            foreach ($layers as $key => $layer) {

                $layer->resizeInPourcent($widthResizePourcent, $heightResizePourcent);
                $this->layers[$key] = $layer;
            }

            // Resize the layer

            $this->resizeBackground($newWidth, $newHeight);
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
        $this->resizeByLargestSide('pixel', $newLargestSideWidth, $converseProportion);
    }

    /**
     * Resize the layer by its largest side by specifying pourcent
     *
     * @param integer $newLargestSideWidth pourcent
     * @param boolean $converseProportion
     */
    public function resizeByLargestSideInPourcent($newLargestSideWidth, $converseProportion = false)
    {
        $this->resizeByLargestSide('pourcent', $newLargestSideWidth, $converseProportion);
    }

    /**
     * Resize the layer by its largest side
     *
     * @param string $unit
     * @param integer $newLargestSideWidth pourcent
     * @param boolean $converseProportion
     */
    public function resizeByLargestSide($unit = "pixel", $newLargestSideWidth, $converseProportion = false)
    {
        if ($unit == 'pourcent') {

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
        $this->resizeByNarrowSide('pixel', $newNarrowSideWidth, $converseProportion);
    }

    /**
     * Resize the layer by its narrow side by specifying pourcent
     *
     * @param integer $newNarrowSideWidth pourcent
     * @param boolean $converseProportion
     */
    public function resizeByNarrowSideInPourcent($newNarrowSideWidth, $converseProportion = false)
    {
        $this->resizeByNarrowSide('pourcent', $newNarrowSideWidth, $converseProportion);
    }

    /**
     * Resize the layer by its narrow side
     *
     * @param string $unit
     * @param integer $newNarrowSideWidth
     * @param boolean $converseProportion
     */
    public function resizeByNarrowSide($unit = "pixel", $newNarrowSideWidth, $converseProportion = false)
    {
        if ($unit == 'pourcent') {

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
     * @param string $backgroundColor
     */
    public function cropInPixel($width = 0, $height = 0, $positionX = 0, $positionY = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        $this->crop("pixel", $width, $height, $positionX, $positionY, $position, $backgroundColor);
    }

    /**
     * Crop the document by specifying pourcent
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param float $pourcentWidth
     * @param float $pourcentHeight
     * @param float $positionXPourcent
     * @param float $positionYPourcent
     * @param string $position
     * @param string $backgroundColor
     */
    public function cropInPourcent($pourcentWidth = 0, $pourcentHeight = 0, $positionXPourcent = 0, $positionYPourcent = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        $this->crop("pourcent", $pourcentWidth, $pourcentHeight, $positionXPourcent, $positionYPourcent, $position, $backgroundColor);
    }

    /**
     * Crop the document
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param string $unit
     * @param float $width
     * @param float $height
     * @param float $positionX
     * @param float $positionY
     * @param string $position
     * @param string $backgroundColor
     */
    public function crop($unit = "pixel", $width = 0, $height = 0, $positionX = 0, $positionY = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        if ($unit == "pourcent") {

            $width = round(($width / 100) * $this->width);
            $height = round(($height / 100) * $this->height);

            $positionX = round(($positionX / 100) * $this->width);
            $positionY = round(($positionY / 100) * $this->height);
        }
        
        if (($width != $this->width || $positionX == 0) || ($height != $this->height || $positionY == 0)) {
            
            $layerTmp = new static(array(
                'width' => $width,
                'height' => $height,
                'backgroundColor' => $backgroundColor,
            ));
            
            $layerClone = new static(array(
                'width' => $this->width,
                'height' => $this->height,
            ));
            
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
     * @param string $backgroundColor
     */
    public function cropMaximumInPixel($positionX = 0, $positionY = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        $this->cropMaximum("pixel", $positionX, $positionY, $position, $backgroundColor);
    }

    /**
     * Crop the maximum possible from left top ("LT"), "RT"... by specifying a shift in pourcent
     *
     * $backgroundColor can be set transparent (but script could be long to execute)
     * $position: http://phpimageworkshop.com/doc/22/corners-positions-schema-of-an-image.html
     *
     * @param integer $width
     * @param integer $height
     * @param integer $positionXPourcent
     * @param integer $positionYPourcent
     * @param string $position
     * @param string $backgroundColor
     */
    public function cropMaximumInPourcent($positionXPourcent = 0, $positionYPourcent = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        $this->cropMaximum("pourcent", $positionXPourcent, $positionYPourcent, $position, $backgroundColor);
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
     * @param string $backgroundColor
     */
    public function cropMaximum($unit = "pixel", $positionX = 0, $positionY = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        $narrowSide = $this->getNarrowSideWidth();
        if ($unit == "pourcent") {

            $positionX = round(($positionX / 100) * $this->width);
            $positionY = round(($positionY / 100) * $this->height);
        }

        $this->cropInPixel($narrowSide, $narrowSide, $positionX, $positionY, $position, $backgroundColor);
    }

    /**
     * Rotate layer
     *
     * @param integer $layerId
     * @param integer $degree
     */
    public function rotateLayer($layerId, $degree)
    {
        $this->layers[$layerId]->rotate($degree);
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

			// Rotate the layer background image
            $imageRotated = imagerotate($this->image, -$degrees, -1);
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
                    "x" => $layer->width / 2,
                    "y" => $layer->height / 2,
                );

                $smallImageCenter = array(
                    "x" => $layerSelfOldCenterPosition["x"] + $this->layerPositions[$layerId]["x"],
                    "y" => $layerSelfOldCenterPosition["y"] + $this->layerPositions[$layerId]["y"],
                );

                $this->layers[$layerId]->rotate($degrees);

                $ro = sqrt(pow($smallImageCenter["x"], 2) + pow($smallImageCenter["y"], 2));

                $teta = (acos($smallImageCenter["x"] / $ro)) * 180 / pi();

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
                    "x" => $newPositionX,
                    "y" => $newPositionY,
                );
            }
        }
    }

    /**
     * Change the opacity of a layer
     * $recursive: apply it on sublayers
     *
     * @param integer $layerId
     * @param integer $opacity
     * @param boolean $recursive
     */
    public function layerOpacity($layerId, $opacity, $recursive = true)
    {
        $this->layers[$layerId]->opacity($opacity, $recursive);
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

        $transparentImage = static::generateImage($this->getWidth(), $this->getHeight());

        static::imagecopymergealpha($transparentImage, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $opacity);

        unset($this->image);
        $this->image = $transparentImage;
        unset($transparentImage);
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
    public function writeText($text, $font = 1, $color = "ffffff", $positionX = 0, $positionY = 0, $align = "horizontal")
    {
        $RGBTextColor = static::convertHexToRGB($color);
        $textColor = imagecolorallocate($this->image, $RGBTextColor["R"], $RGBTextColor["G"], $RGBTextColor["B"]);

        if ($align == "horizontal") {

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
    public function write($text, $fontPath, $fontSize = 13, $color = "ffffff", $positionX = 0, $positionY = 0, $fontRotation = 0)
    {
        $RGBTextColor = static::convertHexToRGB($color);
        $textColor = imagecolorallocate($this->image, $RGBTextColor["R"], $RGBTextColor["G"], $RGBTextColor["B"]);

        return imagettftext($this->image, $fontSize, $fontRotation, $positionX, $positionY, $textColor, $fontPath, $text);
    }

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
            if ($this->layerPositions[$layerId]["x"] != 0 || $this->layerPositions[$layerId]["y"] != 0) {

                $virginLayoutImageTmp = static::generateImage($this->width, $this->height);
                static::mergeTwoImages($virginLayoutImageTmp, $imagesToMerge[$layerLevel], $this->layerPositions[$layerId]["x"], $this->layerPositions[$layerId]["y"], 0, 0);
                $imagesToMerge[$layerLevel] = $virginLayoutImageTmp;
                unset($virginLayoutImageTmp);
            }
        }

        $iterator = 1;
        $mergedImage = $this->image;
        ksort($imagesToMerge);

        foreach ($imagesToMerge as $imageLevel => $image) {

            static::mergeTwoImages($mergedImage, $image);

            $iterator++;
        }
        
        $opacity = 127;
        
        if ($backgroundColor) {
            $opacity = 0;
        }
        
        $backgroundImage = static::generateImage($this->width, $this->height, $backgroundColor, $opacity);
        static::mergeTwoImages($backgroundImage, $mergedImage);
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
     */
    public function save($folder, $imageName, $createFolders = true, $backgroundColor = null, $imageQuality = 75)
    {
        if (!is_file($folder)) {

            if (is_dir($folder) || $createFolders) {

                // Creating the folders if they don't exist
                if (!is_dir($folder) && $createFolders) {
                    $oldUmask = umask(0);
                    mkdir($folder, 0777, true);
                    umask($oldUmask);
                    chmod($folder, 0777);
                }

                $extension = explode(".", $imageName);
                $extension = strtolower($extension[count($extension) - 1]);

                $filename = $folder."/".$imageName;

                if (($extension == "jpg" || $extension == "jpeg" || $extension == "gif") && (!$backgroundColor || $backgroundColor == "transparent")) {
                    $backgroundColor = "ffffff";
                }

                $image = $this->getResult($backgroundColor);

                if ($extension == "jpg" || $extension == "jpeg") {

                    imagejpeg($image, $filename, $imageQuality);
                    unset($image);

                } elseif ($extension == "gif") {

                    imagegif($image, $filename);
                    unset($image);

                } elseif ($extension == "png") {

                    $imageQuality = $imageQuality / 10;
                    $imageQuality -= 1;

                    imagepng($image, $filename, $imageQuality);
                    unset($image);
                }
            }
        }
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
    
    /**
     * Apply horizontal or vertical flip. (Transformation)
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
        
        $temp = static::generateImage($this->width, $this->height);
        
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

    // Internals
    // ===================================================================================
    
    /**
     * Update the positions of layers in the stack after cropping
     *
     * @param integer $positionX
     * @param integer $positionY
     */
    public function updateLayerPositionsAfterCropping($positionX, $positionY)
    {
        foreach ($this->layers as $layerId => $layer) {

            $oldLayerPosX = $this->layerPositions[$layerId]["x"];
            $oldLayerPosY = $this->layerPositions[$layerId]["y"];

            $newLayerPosX = $oldLayerPosX + $positionX;
            $newLayerPosY = $oldLayerPosY + $positionY;

            unset($this->layerPositions[$layerId]);
            
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

        $virginLayoutImage = static::generateImage($this->width, $this->height);

        imagecopyresampled($virginLayoutImage, $this->image, 0, 0, 0, 0, $this->width, $this->height, $oldWidth, $oldHeight);

        unset($this->image);
        $this->image = $virginLayoutImage;
    }

    /**
     * Called to initialize a virgin image var
     *
     * @param string $backgroundColor
     */
    public function initializeImage($backgroundColor = null)
    {
        unset($this->image);

        if ($backgroundColor) {

            $this->image = static::generateImage($this->width, $this->height, $backgroundColor, 0);

        } else {

            $this->image = static::generateImage($this->width, $this->height);
        }
    }

    /**
     * Called to initialize the image var from a given path
     * (Update layer width and height)
     *
     * @param String $path
     */
    public function initializeImageFrom($path)
    {
        unset($this->image);

        if (file_exists($path)) {

            $imageSizeInfos = getimagesize($path);

            $this->width = $imageSizeInfos[0];
            $this->height = $imageSizeInfos[1];

            $mimeContentType = explode("/", $imageSizeInfos["mime"]);
            $mimeContentType = $mimeContentType[1];

            switch ($mimeContentType) {

                case "jpeg":
                    $this->image = imagecreatefromjpeg($path);
                break;

                case "gif":
                    $this->image = imagecreatefromgif($path);
                break;

                case "png":
                    $this->image = imagecreatefrompng($path);
                break;

                default:
                    echo 'Not an image file (jpeg/png/gif) at "'.$path.'"'; exit;
                break;
            }

        } else {
            echo 'No such file found at "'.$path.'"'; exit;
        }
    }

    /**
     * Called to initialize the image var from a php image var
     * (Update layer width and height)
     *
     * @param resource $image
     */
    public function initializeImageWith($image)
    {
        unset($this->image);

        if (gettype($image) != "resource") {
            echo "You must give a php image var by using initializeImageWith"; exit;
        }

        $this->width = imagesx($image);
        $this->height = imagesy($image);
        $this->image = $image;
    }

    /**
     * Called to initialize a text layer
     * (Update layer width and height)
     *
     * @param string $text
     * @param string $fontPath
     * @param integer $fontSize
     * @param string $fontColor
     * @param integer $textRotation
     * @param integer $backgroundColor
     */
    public function initializeTextImage($text, $fontPath, $fontSize = 13, $fontColor = "ffffff", $textRotation = 0, $backgroundColor = null)
    {
        unset($this->image);

        $textDimensions = ImageWorkshop::getTextBoxDimension($fontSize, $textRotation, $fontPath, $text);

        $this->width = $textDimensions["width"];
        $this->height = $textDimensions["height"];

        if ($backgroundColor) {

            $this->image = static::generateImage($this->width, $this->height, $backgroundColor, 0);

        } else {

            $this->image = static::generateImage($this->width, $this->height);
        }

        $this->write($text, $fontPath, $fontSize, $fontColor, $textDimensions["left"], $textDimensions["top"], $textRotation);
    }

    /**
     * Index a sublayer in the layer stack
     * Return an array containing the generated sublayer id and its corrected level:
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
        $this->layerPositions[$layerId] = static::calculatePositions($this->getWidth(), $this->getHeight(), $layer->getWidth(), $layer->getHeight(), $positionX, $positionY, $position);

        // Update the lastLayerId of the workshop
        $this->lastLayerId = $layerId;

        // Add the layer level in the stack
        $layerLevel = $this->indexLevelInDocument($layerLevel, $layerId);

        return array(
            "layerLevel" => $layerLevel,
            "id" => $layerId,
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
        // Level already exists
        if (array_key_exists($layerLevel, $this->layerLevels)) {

            // All layers after this level and the layer which have this level are updated
            ksort($this->layerLevels);
            $layerLevelsTmp = $this->layerLevels;

            foreach ($layerLevelsTmp as $levelTmp => $layerIdTmp) {

                if ($levelTmp >= $layerLevel) {
                    $this->layerLevels[$levelTmp + 1] = $layerIdTmp;
                }
            }

            unset($layerLevelsTmp);

        } else { // Level isn't taken

            // If given level is too high, proceed adjustement
            if ($this->highestLayerLevel < $layerLevel) {
                $layerLevel = $this->highestLayerLevel + 1;
            }
        }

        $this->layerLevels[$layerLevel] = $layerId;

        // Update $highestLayerLevel
        $this->highestLayerLevel = max(array_flip($this->layerLevels));

        return $layerLevel;
    }

    /**
     * Delete the current object
     */
    public function delete()
    {
        imagedestroy($this->image);
        $this->clearStack();
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
        imagecopy($destinationImage, $sourceImage, $destinationPosX, $destinationPosY, $sourcePosX, $sourcePosY, imageSX($sourceImage), imageSY($sourceImage));
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
            "R" => base_convert(substr($hex, 0, 2), 16, 10),
            "G" => base_convert(substr($hex, 2, 2), 16, 10),
            "B" => base_convert(substr($hex, 4, 2), 16, 10),
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
    public static function imagecopymergealpha(&$destImg, &$srcImg, $destX, $destY, $srcX, $srcY, $srcW, $srcH, $pct = 0)
    {
        $destX = (int) $destX;
        $destY = (int) $destY;
        $srcX = (int) $srcX;
        $srcY = (int) $srcY;
        $srcW = (int) $srcW;
        $srcH = (int) $srcH;
        $pct = (int) $pct;
        $destW = imagesx($destImg);
        $destH = imagesy($destImg);

        for ($y = 0; $y < $srcH + $srcY; $y++) {

            for ($x = 0; $x < $srcW + $srcX; $x++) {

                if ($x + $destX >= 0 && $x + $destX < $destW && $x + $srcX >= 0 && $x + $srcX < $srcW && $y + $destY >= 0 && $y + $destY < $destH && $y + $srcY >= 0 && $y + $srcY < $srcH) {

                    $destPixel = imagecolorsforindex($destImg, imagecolorat($destImg, $x + $destX, $y + $destY));
                    $srcImgColorat = imagecolorat($srcImg, $x + $srcX, $y + $srcY);
                    
                    if ($srcImgColorat > 0) {
                    
                        $srcPixel = imagecolorsforindex($srcImg, $srcImgColorat);
    
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
                            $color = imagecolorallocatealpha($destImg, $red, $green, $blue, $alpha);
                            imagesetpixel($destImg, $x + $destX, $y + $destY, $color);
                        }
                    }
                }
            }
        }
    }

    /**
     * Reset the layer stack
     */
    public function clearStack()
    {
        foreach ($this->layers as $layer) {
            $layer->delete();
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

        // destroy img
        imagedestroy($img);

        return array(
            "left" => $left - $rleft,
            "top" => $top - $rtop,
            "width" => $rright - $rleft + 1,
            "height" => $rbottom - $rtop + 1,
        );
    }

    /**
     * Create a new background image var from the old background image var
     */
    public function createNewVarFromBackgroundImage()
    {
        // Creation of a new background image
        $virginImage = static::generateImage($this->getWidth(), $this->getHeight());

        static::mergeTwoImages($virginImage, $this->image, 0, 0, 0, 0);
        unset($this->image);

        $this->image = $virginImage;
        unset($virginImage);

        $layers = $this->layers;

        foreach($layers as $layerId => $layer) {

            $this->layers[$layerId] = clone $this->layers[$layerId];
        }
    }

    /**
     * Get a layer in the stack
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
     * Calculate the right positions of a layer in a parent container (layer)
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
    public static function calculatePositions($containerWidth, $containerHeight, $layerWidth, $layerHeight, $layerPositionX, $layerPositionY, $position = "LT")
    {
        $position = strtolower($position);

        if ($position == "rt") {

            $layerPositionX = $containerWidth - $layerWidth - $layerPositionX;

        } elseif ($position == "lb") {

            $layerPositionY = $containerHeight - $layerHeight - $layerPositionY;

        } elseif ($position == "rb") {

            $layerPositionX = $containerWidth - $layerWidth - $layerPositionX;
            $layerPositionY = $containerHeight - $layerHeight - $layerPositionY;

        } elseif ($position == "mm") {

            $layerPositionX = (($containerWidth - $layerWidth) / 2) + $layerPositionX;
            $layerPositionY = (($containerHeight - $layerHeight) / 2) + $layerPositionY;

        } elseif ($position == "mt") {

            $layerPositionX = (($containerWidth - $layerWidth) / 2) + $layerPositionX;

        } elseif ($position == "mb") {

            $layerPositionX = (($containerWidth - $layerWidth) / 2) + $layerPositionX;
            $layerPositionY = $containerHeight - $layerHeight - $layerPositionY;

        } elseif ($position == "lm") {

            $layerPositionY = (($containerHeight - $layerHeight) / 2) + $layerPositionY;

        } elseif ($position == "rm") {

            $layerPositionX = $containerWidth - $layerWidth - $layerPositionX;
            $layerPositionY = (($containerHeight - $layerHeight) / 2) + $layerPositionY;
        }

        return array(
            "x" => $layerPositionX,
            "y" => $layerPositionY,
        );
    }

    // Getter / Setter
    // ===================================================================================

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
}
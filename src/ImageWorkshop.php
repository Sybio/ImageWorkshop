<?php

/**
 * ImageWorkshop class
 * 
 * Powerful PHP class using GD library to work easily with images including layer notion (like Photoshop or GIMP).
 * ImageWorkshop can be used as a layer, a group or a document.
 * 
 * @version 1.0
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright Clément Guillemain
 */
class ImageWorkshop
{
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
     * Layers and Groups
     */
    public $layers;
    
    /**
     * @var layersLevels
     * 
     * Levels positions of the sublayers in the stack
     */
    protected $layersLevels;
    
    /**
     * @var layersPositions
     * 
     * Positions (x and y) of the sublayers in the stack
     */
    protected $layersPositions;
    
    /**
     * @var lastLayerId
     * 
     * Id of the last indexes sublayer
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
     * Constructor
     * 
     * @param array $params
     */
    public function __construct($params = array())
    {
        $this->width = 800;
        $this->height = 600;
        $imageFromPath = null;
        $imageVar = null;
        $backgroundColor = null;
        $text = null;
        $fontPath = null;
        $fontSize = 13;
        $fontColor = "ffffff";
        $textRotation = 0;
        $fileObject = null;
        $tmpName = null;
        $mimeType = null;
        
        if (array_key_exists("width", $params)) {
            $this->width = $params["width"];
        }
        
        if (array_key_exists("height", $params)) {
            $this->height = $params["height"];
        }
        
        if (array_key_exists("imageFromPath", $params)) {
            $imageFromPath = $params["imageFromPath"];
        }
        
        if (array_key_exists("imageVar", $params)) {
            $imageVar = $params["imageVar"];
        }
        
        if (array_key_exists("backgroundColor", $params)) {
            $backgroundColor = $params["backgroundColor"];
        }
        
        // Text layer
        
        if (array_key_exists("text", $params)) {
            $text = $params["text"];
        }
        
        if (array_key_exists("fontPath", $params)) {
            $fontPath = $params["fontPath"];
        }
        
        if (array_key_exists("fontSize", $params)) {
            $fontSize = $params["fontSize"];
        }
        
        if (array_key_exists("fontColor", $params)) {
            $fontColor = $params["fontColor"];
        }
        
        if (array_key_exists("textRotation", $params)) {
            $textRotation = $params["textRotation"];
        }
        
        // Uploaded file object layer
        
        if (array_key_exists("fileObject", $params)) {
            
            $fileObject = $params["fileObject"];
            
        } elseif (array_key_exists("tmpName", $params)) {
            
            $tmpName = $params["tmpName"];
        }
        
        $this->clearStack();
        
        // Initialization of the layer dimensions and background image
        
        if ($imageFromPath || $fileObject || $tmpName) {
            
            if ($fileObject) {
                
                $imageFromPath = $fileObject["tmp_name"];
                
            } elseif ($tmpName) {
                
                $imageFromPath = $tmpName;
            }
            
            $this->initializeImageFrom($imageFromPath);
            
        } elseif ($imageVar) {
            
            $this->initializeImageWith($imageVar);
            
        } elseif ($text && $fontPath) {
            
            $this->initializeTextImage($text, $fontPath, $fontSize, $fontColor, $textRotation, $backgroundColor);
            
        } else {
            
            $this->initializeImage($backgroundColor);
        }
    }
    
    // Methods ##################################################################################
    
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
     * @todo revoir le imagecopy (transparence ?)
     * 
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
     * Add an existing ImageWorkshop and set it in the document for a given level
     * Return an array containing the generated layer Id for the indexed layer and its corrected level:
     * array("layerLevel" => integer, "id" => integer)
     * 
     * $position: LT ($positionX pixels from the left and $positionY pixels from the top), RT (Right top), LB (Left bottom), RB (Right bottom)
     * MM (Place the layer center in the middle of the document)
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
     * @todo
     * 
     * Merge a layer with another layer under it on the stack
     * Note: the result layer will conserve the given id 
     * Return true if success or false if layer isn't found or don't has a layer under it
     * 
     * @param integer $layerId
     * 
     * @return boolean
     */
    public function mergeDown($layerId)
    {
        // if the layer exists in document
        /*if ($this->isLayerInIndex($layerId)) {
        
            // If there is a layer under it
            if ($subLayer = todo) {
                
                $this->layers[$layerId]->merge();
                
                $subLayer->merge();
                
                // merge $layer with $subLayer 
                // TODO
                
                // remove $subLayer from index and from level index
                // TODO
                
                return true;
            }
        
        }*/
        
        return false;
    }
    
    /**
     * Merge sublayers on the layer background
     */
    public function mergeAll()
    {
        $this->image = $this->getResult();
        $this->clearStack();
    }
    
    /**
     * Move a sublayer on the top of a group stack
     * Return layer level if success or false otherwise
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
     * Return layer level if success or false otherwise
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
     * Return layer level if success or false if layer isn't found
     * 
     * Set $insertUnderTargetedLayer true if you want to insert the layer under the other sublayer at the targeted level,
     * or false to insert on the top of other sublayer at the targeted level
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
                
                ksort($this->layersLevels);
                $layersLevelsTmp = $this->layersLevels;
                
                if ($isOnTopAndNewLevelLower) {
                    $level++;
                }
                
                for ($i = $incrementorStartingValue; $i < $stopLoopWhenSmallerThan; $i++) {
                    
                    if ($isUnderAndNewLevelHigher || $isOnTopAndNewLevelHigher) {
                        
                        $this->layersLevels[$i] = $layersLevelsTmp[$i + 1];
                        
                    } else {
                        
                        $this->layersLevels[$i + 1] = $layersLevelsTmp[$i];
                    }
                }
                
                unset($layersLevelsTmp);
                
                if ($isUnderAndNewLevelHigher) {
                    $level--;
                }
                
                $this->layersLevels[$level] = $layerId;
                
                return $level;
 
            } else {
 
                return $level;
            }
        }
        
        return false;
    }
    
    /**
     * Move up a layer in the stack (level +1)
     * Return layer level if success, false otherwise
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
     * Move down a layer in the stack (level -1)
     * Return layer level if success, false otherwise
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
     * @todo
     * 
     * Delete a layer
     * 
     * @param integer $layerId
     */
    public function remove($layerId)
    {
        // if the layer exists in document
        if ($this->isLayerInIndex($layerId)) {
            
            $layerToDeleteLevel = $this->getLayerLevel($layerId);
            
            // delete
            $this->layers[$layerId]->delete();
            unset($this->layers[$layerId]);
            unset($this->layersLevels[$layerToDeleteLevel]);
            unset($this->layersPositions[$layerId]);
                  
            // One or plural layers are sub of the deleted layer
            if (array_key_exists(($layerToDeleteLevel + 1), $this->layersLevels)) {
                
                ksort($this->layersLevels);
                
                $layersLevelsTmp = $this->layersLevels;
                
                $maxOldestLevel = 1;
                foreach ($layersLevelsTmp as $levelTmp => $layerIdTmp) {
                    
                    if ($levelTmp > $layerToDeleteLevel) {
                        $this->layersLevels[($levelTmp - 1)] = $layerIdTmp;
                    }
                    $maxOldestLevel++;
                }
                unset($layersLevelsTmp);
                unset($this->layersLevels[$maxOldestLevel]);
            }
            
            // If the deleted layer has the highest level
            if ($layerToDeleteLevel == $this->highestLayerLevel) {
                $this->highestLayerLevel -= 1;
            }
            
        }
    }
    
    /**
     * Get the level of a layer
     * Return layer level if success or false if layer isn't found
     * 
     * @param integer $layerId
     * 
     * @return mixed
     */
    public function getLayerLevel($layerId)
    {
        // if the layer exists in document
        if ($this->isLayerInIndex($layerId)) {
            return array_search($layerId, $this->layersLevels);
        }
        
        return false;
    }
    
    /**
     * Check if a layer exists for a given id
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
        $RGBColors = self::convertHexToRGB($color);
        
        $image = imagecreatetruecolor($width, $height);
        imagesavealpha($image, true);
        $color = imagecolorallocatealpha($image, $RGBColors["R"], $RGBColors["G"], $RGBColors["B"], $opacity);
        imagefill($image, 0, 0, $color);
        
        return $image;
    }
    
    /**
     * Resize the layer by specifying pixel
     * 
     * If you choose to conserve the proportion but you give $newWidth AND $newHeight, proportion will be still conserve
     * and the resize will use $newWidth to determine the $newHeight
     * 
     * @param integer $newWidth
     * @param integer $newHeight
     * @param boolean $converseProportion
     */
    public function resizeByPixel($newWidth = null, $newHeight = null, $converseProportion = false)
    {
        if ($newWidth || $newHeight) {
            
            if ($converseProportion) { // Proportion are conserved
                
                if ($newWidth) {
                    
                    $widthPourcentScale = $this->width / 100;
                    $widthResizePourcent = $newWidth / $widthPourcentScale;
                    
                    $newHeight = round(($widthResizePourcent / 100) * $this->height);
                    $heightResizePourcent = $widthResizePourcent;
                    
                } else {
                    
                    $heightPourcentScale = $this->height / 100;
                    $heightResizePourcent = $newHeight / $heightPourcentScale;
                    
                    $newWidth = round(($heightResizePourcent / 100) * $this->width);
                    $widthResizePourcent = $heightResizePourcent;
                }
                
            } elseif (($newWidth && !$newHeight) || (!$newWidth && $newHeight)) { // New width OR new height is given
                
                if ($newWidth) {
                    
                    $widthPourcentScale = $this->width / 100;
                    $widthResizePourcent = $newWidth / $widthPourcentScale;
                    
                    $heightResizePourcent = 100;
                    $newHeight = $this->height;
                    
                } else {
                    
                    $heightPourcentScale = $this->height / 100;
                    $heightResizePourcent = $newHeight / $heightPourcentScale;
                    
                    $widthResizePourcent = 100;
                    $newWidth = $this->width;
                }
                
            } else { // New width AND new height are given
                
                $widthPourcentScale = $this->width / 100;
                $widthResizePourcent = $newWidth / $widthPourcentScale;
                
                $heightPourcentScale = $this->height / 100;
                $heightResizePourcent = $newHeight / $heightPourcentScale;
            }
            
            // Update the layer positions in the stack
            
            $layersPositions = $this->layersPositions;
            
            foreach ($layersPositions as $layerId => $layerPositions) {
                
                $newPosX = round(($widthResizePourcent / 100) * $layerPositions['x']);
                $newPosY = round(($heightResizePourcent / 100) * $layerPositions['y']);
                
                $this->layersPositions[$layerId] = array(
                    "x" => $newPosX,
                    "y" => $newPosY,
                );
            }
            
            // Resize layers in the stack
            
            $layers = $this->layers;
                 
            foreach ($layers as $key => $layer) {
                
                $layer->resizeByPourcent($widthResizePourcent, $heightResizePourcent);
                $this->layers[$key] = $layer;
            }
            
            // Resize the layer
            
            $this->resizeBackground($newWidth, $newHeight);
        }
    }
    
    /**
     * Resize the layer by specifying a pourcent
     * 
     * @param float $pourcentWidth
     * @param float $pourcentHeight
     * @param boolean $converseProportion
     */
    public function resizeByPourcent($pourcentWidth = null, $pourcentHeight = null, $converseProportion = false)
    {
        if ($pourcentWidth || $pourcentHeight) {
            
            if ($converseProportion) { // converse proportion
                
                if ($pourcentWidth) {
                    
                    $pourcentHeight = $pourcentWidth;
                    
                } else {
                    
                    $pourcentWidth = $pourcentHeight;
                }
                
            } elseif (($pourcentWidth && !$pourcentHeight) || (!$pourcentWidth && $pourcentHeight)) { // $pourcentWidth OR $pourcentHeight is given
                
                if ($pourcentWidth) {
                    
                    $pourcentHeight = 100;
                    
                } else {
                    
                    $pourcentWidth = 100;
                }
                
            }
            
            $newWidth = round($this->width * ($pourcentWidth / 100));
            $newHeight = round($this->height * ($pourcentHeight / 100));
            
            // Update the layer positions in the stack
            
            $layersPositions = $this->layersPositions;
            
            foreach ($layersPositions as $layerId => $layerPositions) {
                
                $newPosX = round(($pourcentWidth / 100) * $layerPositions['x']);
                $newPosY = round(($pourcentHeight / 100) * $layerPositions['y']);
                
                $this->layersPositions[$layerId] = array(
                    "x" => $newPosX,
                    "y" => $newPosY,
                );
            }
            
            // Resize layers in the stack
            
            $layers = $this->layers;
                        
            foreach ($layers as $key => $layer) {
                
                $layer->resizeByPourcent(null, $pourcentWidth, $pourcentHeight);
                $this->layers[$key] = $layer;
            }
            
            // Resize the layer
            
            $this->resizeBackground($newWidth, $newHeight);
        }
    }
    
    /**
     * Crop the document by specifying pixels
     * 
     * $backgroundColor can be set transparent (but script could be long to execute)
     * 
     * @param integer $width
     * @param integer $height
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     * @param string $backgroundColor
     */
    public function cropByPixel($width = 0, $height = 0, $positionX = 0, $positionY = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        $this->crop("pixel", $width, $height, $positionX, $positionY, $position, $backgroundColor);
    }
    
    /**
     * Crop the document by specifying pourcent
     * 
     * $backgroundColor can be set transparent (but script could be long to execute)
     * 
     * @param float $pourcentWidth
     * @param float $pourcentHeight
     * @param float $positionXPourcent
     * @param float $positionYPourcent
     * @param string $position
     * @param string $backgroundColor
     */
    public function cropByPourcent($pourcentWidth = 0, $pourcentHeight = 0, $positionXPourcent = 0, $positionYPourcent = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        $this->crop("pourcent", $pourcentWidth, $pourcentHeight, $positionXPourcent, $positionYPourcent, $position, $backgroundColor);
    }
    
    /**
     * Crop the document
     * 
     * $backgroundColor can be set transparent (but script could be long to execute)
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
        
        if ($position == "RT") {
            
            $positionX = $this->getWidth() - $width - $positionX;
            
        } elseif ($position == "LB") {
            
            $positionY = $this->getHeight() - $height - $positionY;
            
        } elseif ($position == "RB") {
            
            $positionX = $this->getWidth() - $width - $positionX;
            $positionY = $this->getHeight() - $height - $positionY;
            
        } elseif ($position == "MM") {
            
            $positionX = (($this->getWidth() - $width) / 2) + $positionX;
            $positionY = (($this->getHeight() - $height) / 2) + $positionY;
            
        } elseif ($position == "MT") {
            
            $positionX = (($this->getWidth() - $width) / 2) + $positionX;
            
        } elseif ($position == "MB") {
            
            $positionX = (($this->getWidth() - $width) / 2) + $positionX;
            $positionY = $this->getHeight() - $height - $positionY;
            
        } elseif ($position == "LM") {
            
            $positionY = (($this->getHeight() - $height) / 2) + $positionY;
            
        } elseif ($position == "RM") {
            
            $positionX = $this->getWidth() - $width - $positionX;
            $positionY = (($this->getHeight() - $height) / 2) + $positionY;
        }
        
        $this->updateLayerPositionsAfterCropping($positionX, $positionY);
        
        $this->cropBackground($width, $height, $positionX, $positionY, $position, $backgroundColor);
    }

    /**
     * Crop the maximum possible from left top ("LT"), "RT"... by specifying a shift in pixel
     * 
     * $backgroundColor can be set transparent (but script could be long to execute)
     * 
     * @param integer $width
     * @param integer $height
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     * @param string $backgroundColor
     */
    public function cropMaximumByPixel($positionX = 0, $positionY = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        $this->cropMaximum("pixel", $smallestSideWidth, $smallestSideWidth, $positionX, $positionY, $position, $backgroundColor);
    }
    
    /**
     * Crop the maximum possible from left top ("LT"), "RT"... by specifying a shift in pourcent
     * 
     * $backgroundColor can be set transparent (but script could be long to execute)
     * 
     * @param integer $width
     * @param integer $height
     * @param integer $positionXPourcent
     * @param integer $positionYPourcent
     * @param string $position
     * @param string $backgroundColor
     */
    public function cropMaximumByPourcent($positionXPourcent = 0, $positionYPourcent = 0, $position = "LT", $backgroundColor = "ffffff")
    {
        $this->cropMaximum("pourcent", $positionXPourcent, $positionYPourcent, $position, $backgroundColor);
    }
    
    /**
     * Crop the maximum possible from left top
     * 
     * $backgroundColor can be set transparent (but script could be long to execute)
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
        // We determine the smallest side
        $smallestSideWidth = $this->getWidth();
        
        if ($this->getHeight() < $smallestSideWidth) {
            $smallestSideWidth = $this->getHeight();
        }
        
        if ($unit == "pourcent") {
            
            $positionX = round(($positionX / 100) * $this->width);
            $positionY = round(($positionY / 100) * $this->height);
        }
                
        $this->cropByPixel($smallestSideWidth, $smallestSideWidth, $positionX, $positionY, $position, $backgroundColor);
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
    
    /* REFACTO à faire mais fonctionnne */
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
            
            $imageRotated = imagerotate($this->image, -$degrees, -1);
    
            imagealphablending($imageRotated, true);
            imagesavealpha($imageRotated, true);
            
            unset($this->image);
            
            $this->image = $imageRotated;
            
            $oldWidth = $this->width;
            $oldHeight = $this->height;
            
            $this->width = imagesx($this->image);
            $this->height = imagesy($this->image);
            
            $centreGrandeImage = array(
                "x" => 0,
                "y" => 0,
            );
            
            $centreNouvelleGrandeImage = array(
                "x" => 0,
                "y" => 0,
            );
            
            foreach ($this->layers as $layerId => $layer) {
                
                $ancienneLargeur = $layer->width;
                $ancienneHauteur = $layer->height;
                
                $layerSelfOldCenterPosition = array(
                    "x" => $layer->width / 2,
                    "y" => $layer->height / 2,
                );
                
                $centrePetiteImage = array(
                    "x" => $layerSelfOldCenterPosition["x"] + $this->layersPositions[$layerId]["x"],
                    "y" => $layerSelfOldCenterPosition["y"] + $this->layersPositions[$layerId]["y"],
                );
                
                $this->layers[$layerId]->rotate($degrees);
                
                $ro = sqrt(pow($centrePetiteImage["x"], 2) + pow($centrePetiteImage["y"], 2));
                
                $teta = (acos($centrePetiteImage["x"] / $ro)) * 180 / pi();
                
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
                
                $this->layersPositions[$layerId] = array(
                    "x" => $newPositionX,
                    "y" => $newPositionY,
                );
            }
        }
    }
    
    /**
     * Change the opacity of a layer
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
        
        $transparentImage = self::generateImage($this->getWidth(), $this->getHeight());
        
        self::imagecopymergealpha($transparentImage, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $opacity);
        
        unset($this->image);
        $this->image = $transparentImage;
        unset($transparentImage);
    }
    
    /**
     * Add a text on the background image of the layer using a default font
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
        $RGBTextColor = self::convertHexToRGB($color);
        $textColor = imagecolorallocate($this->image, $RGBTextColor["R"], $RGBTextColor["G"], $RGBTextColor["B"]);
        
        if ($align == "horizontal") {
            imagestring($this->image, $font, $positionX, $positionY, $text, $textColor);
        } else {
            imagestringup($this->image, $font, $positionX, $positionY, $text, $textColor);
        }
    }
    
    /**
     * Add a text on the background image of the layer
     * 
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
        $RGBTextColor = self::convertHexToRGB($color);
        $textColor = imagecolorallocate($this->image, $RGBTextColor["R"], $RGBTextColor["G"], $RGBTextColor["B"]);
        
        return imagettftext($this->image, $fontSize, $fontRotation, $positionX, $positionY, $textColor, $fontPath, $text);
    }
    
    /**
     * Return a merged resource image
     * 
     * $backgroundColor is really usefull if you want to save a JPG, because the transparency of the background
     * would be remove for a colored background, so you should choose a color like "ffffff" (white)
     * 
     * @param string $backgroundColor
     * 
     * @return resource
     */
    public function getResult($backgroundColor = null)
    {
        $imagesToMerge = array();
        
        $layoutImage = $this->image;
        
        $virginLayoutImage = self::generateImage($this->width, $this->height);
        
        ksort($this->layersLevels);
        
        foreach ($this->layersLevels as $layerLevel => $layerId) {
            
            $virginLayoutImageTmp = $virginLayoutImage;
                        
            $imagesToMerge[$layerLevel] = $this->layers[$layerId]->getResult();
            
            // Layer position
            if ($this->layersPositions[$layerId]["x"] != 0 || $this->layersPositions[$layerId]["y"] != 0) {
                $imagesToMerge[$layerLevel] = $this->mergeTwoImages($virginLayoutImageTmp, $imagesToMerge[$layerLevel], $this->layersPositions[$layerId]["x"], $this->layersPositions[$layerId]["y"], 0, 0);
            }
            
            unset($virginLayoutImageTmp);
            
        }
        
        $iterator = 1;
        $mergedImage = $layoutImage;
        ksort($imagesToMerge);                
        
        foreach ($imagesToMerge as $imageLevel => $image) {
            
            $mergedImage = $this->mergeTwoImages($mergedImage, $image);
            
            $iterator++;
        }
        
        if ($backgroundColor) {
            
            $backgroundImage = self::generateImage($this->width, $this->height, $backgroundColor, 0);
            $mergedImage = $this->mergeTwoImages($backgroundImage, $mergedImage);
            unset($backgroundImage);
        }
        
        return $mergedImage;
    }
    
    /**
     * Save the resulting image at the specified path
     * 
     * $backgroundColor is really usefull if you want to save a JPG, because the transparency of the background
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
     * Be careful, some filters can damage transparent images, use it sparingly ! (A good pratice is to mergeAll layers before applying a filter)
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
        imagefilter($this->image, $filterType, $arg1, $arg2, $arg3, $arg4);
        
        if ($recursive) {
            
            $layers = $this->layers;
        
            foreach($layers as $layerId => $layer) {
                
                $this->layers[$layerId]->applyFilter($filterType, $arg1, $arg2, $arg3, $arg4, true);
            }
        
        }
    }

    // Internal methods ##################################################################################
    
    /**
     * Update the positions of layers in the stack after cropping
     * 
     * @param integer $positionX
     * @param integer $positionY
     */
    public function updateLayerPositionsAfterCropping($positionX, $positionY)
    {
        foreach ($this->layers as $layerId => $layer) {
            
            $oldLayerPosX = $this->layersPositions[$layerId]["x"];
            $oldLayerPosY = $this->layersPositions[$layerId]["y"];
            
            $newLayerPosX = $oldLayerPosX - $positionX;
            $newLayerPosY = $oldLayerPosY - $positionY;
            
            unset($this->layersPositions[$layerId]);
            
            $this->layersPositions[$layerId] = array(
                "x" => $newLayerPosX,
                "y" => $newLayerPosY,
            );
            
        }
    }
    
    /**
     * Crop the background of a layer
     * $backgroundColor: "ffffff", "transparent"
     * 
     * @param integer $newWidth
     * @param integer $newHeight
     * @param integer $positionX
     * @param integer $positionY
     * @param string $position
     * @param string $backgroundColor
     */
    public function cropBackground($newWidth, $newHeight, $positionX, $positionY, $position = "LT", $backgroundColor = "ffffff")
    {
        if ($newWidth <= $this->width && $newHeight <= $this->height) {
            
            $oldWidth = $this->width;
            $oldHeight = $this->height;
            
            $this->width = $newWidth;
            $this->height = $newHeight;
            
            if (($this->width + $positionX) > $oldWidth || $positionX < 0 || ($this->height + $positionY) > $oldHeight || $positionY < 0) {
                
                if ($backgroundColor == "transparent" || !$backgroundColor) {
                    
                    $virginLayoutImage = self::generateImage($this->width, $this->height);
                    
                    self::imagecopymergealpha($virginLayoutImage, $this->image, 0, 0, $positionX, $positionY, $oldWidth, $oldHeight, 100);
                    
                } else {
                    
                    $virginLayoutImage = self::generateImage($this->width, $this->height, $backgroundColor, 0);
                    
                    self::imagecopymergealpha($virginLayoutImage, $this->image, 0, 0, $positionX, $positionY, $oldWidth, $oldHeight, 100);
                }
                
            } else {
                
                $virginLayoutImage = self::generateImage($this->width, $this->height);
                    
                imagecopymerge($virginLayoutImage, $this->image, 0, 0, $positionX, $positionY, $oldWidth, $oldHeight, 100);
            }
            
            unset($this->image);
            $this->image = $virginLayoutImage;
        
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
        
        $virginLayoutImage = self::generateImage($this->width, $this->height);
        
        imagecopyresized($virginLayoutImage, $this->image, 0, 0, 0, 0, $this->width, $this->height, $oldWidth, $oldHeight);
    
        unset($this->image);
        $this->image = $virginLayoutImage;
    }
    
    /**
     * Called to initialize the image var
     * 
     * @param string $backgroundColor
     */
    public function initializeImage($backgroundColor = null)
    {
        unset($this->image);
        
        if ($backgroundColor) {
            
            $this->image = self::generateImage($this->width, $this->height, $backgroundColor, 0);
            
        } else {
            
            $this->image = self::generateImage($this->width, $this->height);
        }
    }
    
    /**
     * Called to initialize the image var from a given path
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
            
            $this->image = self::generateImage($this->width, $this->height, $backgroundColor, 0);
            
        } else {
            
            $this->image = self::generateImage($this->width, $this->height);
        }
        
        $this->write($text, $fontPath, $fontSize, $fontColor, $textDimensions["left"], $textDimensions["top"], $textRotation);
    }
    
    /**
     * Index a layer
     * Return an array containing the generated layer Id for the indexed layer and its corrected level:
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
        
        $position = strtolower($position);
        
        if ($position == "rt") {
            
            $positionX = $this->getWidth() - $layer->getWidth() - $positionX;
            
        } elseif ($position == "lb") {
            
            $positionY = $this->getHeight() - $layer->getHeight() - $positionY;
            
        } elseif ($position == "rb") {
            
            $positionX = $this->getWidth() - $layer->getWidth() - $positionX;
            $positionY = $this->getHeight() - $layer->getHeight() - $positionY;
            
        } elseif ($position == "mm") {
            
            $positionX = (($this->getWidth() - $layer->getWidth()) / 2) + $positionX;
            $positionY = (($this->getHeight() - $layer->getHeight()) / 2) + $positionY;
            
        } elseif ($position == "mt") {
            
            $positionX = (($this->getWidth() - $layer->getWidth()) / 2) + $positionX;
            
        } elseif ($position == "mb") {
            
            $positionX = (($this->getWidth() - $layer->getWidth()) / 2) + $positionX;
            $positionY = $this->getHeight() - $layer->getHeight() - $positionY;
            
        } elseif ($position == "lm") {
            
            $positionY = (($this->getHeight() - $layer->getHeight()) / 2) + $positionY;
            
        } elseif ($position == "rm") {
            
            $positionX = $this->getWidth() - $layer->getWidth() - $positionX;
            $positionY = (($this->getHeight() - $layer->getHeight()) / 2) + $positionY;
        }
        
        $this->layersPositions[$layerId] = array(
            "x" => $positionX,
            "y" => $positionY
        );
        
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
        if (array_key_exists($layerLevel, $this->layersLevels)) {
            
            // All layers after this level and the layer which have this level are updated
            ksort($this->layersLevels);
            $layersLevelsTmp = $this->layersLevels;
            
            foreach ($layersLevelsTmp as $levelTmp => $layerIdTmp) {
                
                if ($levelTmp >= $layerLevel) {
                    $this->layersLevels[$levelTmp + 1] = $layerIdTmp;
                }
            }
            
            unset($layersLevelsTmp);
            
        } else { // Level isn't taken
            
            // If given level is too high, proceed adjustement
            if ($this->highestLayerLevel < $layerLevel) {
                $layerLevel = $this->highestLayerLevel + 1;
            }
        }
        
        $this->layersLevels[$layerLevel] = $layerId;
        
        // Update $highestLayerLevel
        $this->highestLayerLevel = max(array_flip($this->layersLevels));
        
        return $layerLevel;
    }
    
    /**
     * Delete the current object
     */
    public function delete()
    {
        $this->deleteImage();
        $this->clearStack();
    }
    
    /**
     * Delete the resulting image
     */
    public function deleteImage()
    {
        unset($this->image);
    }
    
    /**
     * A REVOIR
     * Merge two image var
     * 
     * @param resource $destinationImage
     * @param resource $sourceImage
     */
    public function mergeTwoImages($destinationImage, $sourceImage, $destinationPosX = 0, $destinationPosY = 0, $sourcePosX = 0, $sourcePosY = 0)
    {
        $sourceImageX = imagesx($sourceImage);
        $sourceImageY = imagesy($sourceImage);
        
        imagecopy($destinationImage, $sourceImage, $destinationPosX, $destinationPosY, $sourcePosX, $sourcePosY, $sourceImageX, $sourceImageY);
        
        return $destinationImage;
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
     */
    public static function imagecopymergealpha(&$dst_im, &$src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct = 0)
    {
        $dst_x = (int) $dst_x;
        $dst_y = (int) $dst_y;
        $src_x = (int) $src_x;
        $src_y = (int) $src_y;
        $src_w = (int) $src_w;
        $src_h = (int) $src_h;
        $pct   = (int) $pct;
        $dst_w = imagesx($dst_im);
        $dst_h = imagesy($dst_im);

        for ($y = 0; $y < $src_h + $src_y; $y++) {
            for ($x = 0; $x < $src_w + $src_x; $x++) {

                if ($x + $dst_x >= 0 && $x + $dst_x < $dst_w && $x + $src_x >= 0 && $x + $src_x < $src_w
                 && $y + $dst_y >= 0 && $y + $dst_y < $dst_h && $y + $src_y >= 0 && $y + $src_y < $src_h) {

                    $dst_pixel = imagecolorsforindex($dst_im, imagecolorat($dst_im, $x + $dst_x, $y + $dst_y));
                    $src_pixel = imagecolorsforindex($src_im, imagecolorat($src_im, $x + $src_x, $y + $src_y));

                    $src_alpha = 1 - ($src_pixel['alpha'] / 127);
                    $dst_alpha = 1 - ($dst_pixel['alpha'] / 127);
                    $opacity = $src_alpha * $pct / 100;
                    if ($dst_alpha >= $opacity) $alpha = $dst_alpha;
                    if ($dst_alpha < $opacity)  $alpha = $opacity;
                    if ($alpha > 1) $alpha = 1;

                    if ($opacity > 0) {
                        $dst_red   = round(( ($dst_pixel['red']   * $dst_alpha * (1 - $opacity)) ) );
                        $dst_green = round(( ($dst_pixel['green'] * $dst_alpha * (1 - $opacity)) ) );
                        $dst_blue  = round(( ($dst_pixel['blue']  * $dst_alpha * (1 - $opacity)) ) );
                        $src_red   = round((($src_pixel['red']   * $opacity)) );
                        $src_green = round((($src_pixel['green'] * $opacity)) );
                        $src_blue  = round((($src_pixel['blue']  * $opacity)) );
                        $red   = round(($dst_red   + $src_red  ) / ($dst_alpha * (1 - $opacity) + $opacity));
                        $green = round(($dst_green + $src_green) / ($dst_alpha * (1 - $opacity) + $opacity));
                        $blue  = round(($dst_blue  + $src_blue ) / ($dst_alpha * (1 - $opacity) + $opacity));
                        if ($red   > 255) $red   = 255;
                        if ($green > 255) $green = 255;
                        if ($blue  > 255) $blue  = 255;
                        $alpha =  round((1 - $alpha) * 127);
                        $color = imagecolorallocatealpha($dst_im, $red, $green, $blue, $alpha);
                        imagesetpixel($dst_im, $x + $dst_x, $y + $dst_y, $color);
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
        unset($this->layers);
        unset($this->layersLevels);
        unset($this->layersPositions);
        
        $this->lastLayerId = 0;
        $this->layers = array();
        $this->layersLevels = array();
        $this->layersPositions = array();
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
     * @return array
     */
    public static function getTextBoxDimension($fontSize, $fontAngle, $fontFile, $text)
    {
        $box = imagettfbbox($fontSize, $fontAngle, $fontFile, $text); 
        
		if(!$box) {
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
		
        // to calculate the exact bounding box i write the text in a large image 
        $img = @imagecreatetruecolor($width << 2, $height << 2); 
        $white = imagecolorallocate($img, 255, 255, 255); 
        $black = imagecolorallocate($img, 0, 0, 0); 
        imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black);
		
        // for sure the text is completely in the image! 
        imagettftext($img, $fontSize, $fontAngle, $left, $top, $white, $fontFile, $text); 
		
        // start scanning (0=> black => empty) 
        $rleft = $w4 = $width<<2; 
        $rright = 0; 
        $rbottom = 0; 
        $rtop = $h4 = $height<<2; 
        for ($x = 0; $x < $w4; $x++) {
			for ($y = 0; $y < $h4; $y++) {
				if (imagecolorat( $img, $x, $y )) {
					$rleft = min( $rleft, $x ); 
					$rright = max( $rright, $x ); 
					$rtop = min( $rtop, $y ); 
					$rbottom = max( $rbottom, $y ); 
				}
			}
		}
		
        // destroy img and serve the result 
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
        $virginImage = self::generateImage($this->getWidth(), $this->getHeight());
        
        $virginImage = $this->mergeTwoImages($virginImage, $this->image, 0, 0, 0, 0);
        unset($this->image);
        
        $this->image = $virginImage;
        
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
     * Getter layersLevels
     * 
     * @return array
     */
    public function getLayersLevels()
    {
        return $this->layersLevels;
    }
    
    /**
     * Getter layersPositions
     * 
     * @return array
     */
    public function getLayersPositions()
    {
        return $this->layersPositions;
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
?>
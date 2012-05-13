<?php
require_once('../src/ImageWorkshop.php');
 
class ImageWorkshopTest extends PHPUnit_Framework_TestCase
{
    /**
     * @todo
     * 
     * Test pasteImage
     * 
     */
    /*public function testPasteImage()
    {
        $this->assertTrue(false);
    }*/
    
    /**
     * @todo
     * 
     * Test addLayer
     * 
     */
    /*public function testAddLayer()
    {
        $this->assertTrue(false);
    }*/
    
    /**
     * @todo
     * 
     * Test mergeDown
     * 
     */
    /*public function testMergeDown()
    {       
        // Test moveDown on a sublayer not positionned at the lowest level
        
        $layer = $this->initializeLayer(2);
        
        $layer->mergeDown(3);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
        );
        
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveDown on a sublayer positionned at the lowest level
        
        $layer = $this->initializeLayer(2);
        
        $layer->mergeDown(1);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
    }*/
    
    /**
     * Test mergeAll
     */
    public function testMergeAll()
    {
        $layer = $this->initializeLayer(2);
        
        $layer->mergeAll();
        
        $layerPositions = $layer->getLayersPositions();
        $layersLevels = $layer->getLayersLevels();
        $highestLayerLevel = $layer->getHighestLayerLevel();
        $lastLayerId = $layer->getLastLayerId();
        
        $array = array();
        
        $this->assertEquals($layerPositions, $array, 'Expect $layerPositions to be an empty array');
        
        $this->assertEquals($layerPositions, $array, 'Expect $layersLevels to be an empty array');
        
        $this->assertTrue($highestLayerLevel === 0, 'Expect $highestLayerLevel to be 0');
        
        $this->assertTrue($lastLayerId === 0, 'Expect $lastLayerId to be 0');
    }
    
    /**
     * test moveTop
     */
    public function testMoveTop()
    {
        // Test moveTop on a layer positionned not at the level 1 or the highest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveTop(3);
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
        );
        
        $this->assertTrue($returnedPosition === 4, 'Expect $returnedPosition to be 4');
        $this->assertTrue($layer->getLayersLevels() == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTop on a layer positionned at the level 1
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveTop(1);
        
        $array = array(
            1 => 2,
            2 => 3,
            3 => 4,
            4 => 1,
        );
        
        $this->assertTrue($returnedPosition === 4, 'Expect $returnedPosition to be 4');
        $this->assertTrue($layer->getLayersLevels() == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTop on a layer positionned at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveTop(4);
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 4, 'Expect $returnedPosition to be 4');
        $this->assertTrue($layer->getLayersLevels() == $array, 'Expect $layersLevels to be the array $array');
    }
    
    /**
     * Test moveBottom
     */
    public function testMoveBottom()
    {
        // Test moveBottom on a layer positionned not at the level 1
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveBottom(3);
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layer->getLayersLevels() == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveBottom on a layer positionned at the level 1
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveBottom(1);
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layer->getLayersLevels() == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveBottom on a layer positionned at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveBottom(4);
        
        $array = array(
            1 => 4,
            2 => 1,
            3 => 2,
            4 => 3,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layer->getLayersLevels() == $array, 'Expect $layersLevels to be the array $array');
    }
    
    /**
     * @todo test all callback positions
     * Test moveTo
     */
    public function testMoveTo()
    {
        // Move on the bottom of a level _____________________
        
        // Test moveTo: move a sublayer on the bottom of the level x + 1 (same position)
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 4, true);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the position at level -10
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, -10, true);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the position at level 0
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 0, true);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
                        
        // Test moveTo: move a sublayer on the bottom of the position at level x - 1
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 2, true);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the position at level x - 2
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(4, 2, true);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 4,
            3 => 2,
            4 => 3,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the first level
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 1, true);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of a higher level
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 5, true);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 4, 'Expect $returnedPosition to be 4');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of a level highest the highest level
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 6, true);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 5,
            5 => 3,
        );
        
        $this->assertTrue($returnedPosition === 5, 'Expect $returnedPosition to be 5');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Move on the top of a level _____________________
        
        // Test moveTo: move a sublayer on the top of the level x + 1
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 4, false);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 4, 'Expect $returnedPosition to be 4');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the position at level 0
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 0, false);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of the position at level -10
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, -10, false);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of the position at level x - 1 (same position)
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 2, false);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of the position at level x - 2
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(4, 2, false);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of the highest position
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 5, false);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 5,
            5 => 3,
        );
        
        $this->assertTrue($returnedPosition === 5, 'Expect $returnedPosition to be 5');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of another position
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 1, false);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of a level highest the highest level
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 6, false);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 5,
            5 => 3,
        );
        
        $this->assertTrue($returnedPosition === 5, 'Expect $returnedPosition to be 5');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
    }
    
    /**
     * Test moveUp
     * 
     */
    public function testMoveUp()
    {
        // Test moveUp on a sublayer at the level 1
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveUp(1);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 2,
            2 => 1,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveUp on a sublayer not positionned at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveUp(2);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveUp on a the sublayer at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveUp(4);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 4, 'Expect $returnedPosition to be 4');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
    }
    
    /**
     * @todo
     * 
     * Test moveDown
     * 
     */
    public function testMoveDown()
    {
        // Test moveDown on a sublayer not positionned at the lowest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveDown(3);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveDown on a sublayer at the level 1
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveDown(1);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveDown on a sublayer at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveDown(4);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
    }
    
    /**
     * @todo
     * 
     * Test remove
     * 
     */
    /*public function testRemove()
    {
        $this->assertTrue(false);
    }*/
    
    /**
     * @todo
     * 
     * Test getLayerLevel
     * 
     */
    /*public function testGetLayerLevel()
    {
        $this->assertTrue(false);
    }*/
    
    /**
     * Test isLayerInIndex
     */
    public function testIsLayerInIndex()
    {
        $layer = $this->initializeLayer(2);
        
        $this->assertTrue($layer->isLayerInIndex(3) === true, 'Layer of id 3 must be in the stack');
        
        $this->assertTrue($layer->isLayerInIndex(5) === false, 'Layer of id 5 would not exist in the stack');
    }
    
    /**
     * @todo
     * 
     * Test generateImage
     * 
     */
    /*public function testGenerateImage()
    {
        $this->assertTrue(false);
    }*/
    
    /**
     * @todo
     * 
     * Test resizeByPixel
     * 
     */
    /*public function testResizeByPixel()
    {
        $this->assertTrue(false);
    }*/
    
    /**
     * @todo
     * 
     * Test resizeByPourcent
     * 
     */
    /*public function testResizeByPourcent()
    {
        $this->assertTrue(false);
    }*/
    
    /**
     * Initialize a layer
     * 
     * @param integer $method
     */
    protected function initializeLayer($method = 1)
    {
        $layer = new ImageWorkshop(array(
            "width" => 100,
            "height" => 75,
        ));
        
        switch ($method) {
            
            case 1:
                
            break;
            
            case 2: // Add 4 sublayers in $layer stack
                
                $layer->addLayer(1, $layer);
                $layer->addLayer(2, $layer);
                $layer->addLayer(3, $layer);
                $layer->addLayer(4, $layer);
                
            break;
            
            case 3: // Add 5 sublayers in $layer stack
                
                $layer->addLayer(1, $layer);
                $layer->addLayer(2, $layer);
                $layer->addLayer(3, $layer);
                $layer->addLayer(4, $layer);
                $layer->addLayer(5, $layer);
                
            break;
        }
        
        return $layer;
    }
}
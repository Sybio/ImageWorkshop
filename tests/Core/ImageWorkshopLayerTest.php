<?php

use PHPImageWorkshop\ImageWorkshop as ImageWorkshop;

require_once(__DIR__.'/../autoload.php');
 
/**
 * ImageWorkshopLayerTest class
 * 
 * Tests ImageWorkshopLayer class
 * 
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain  / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 * 
 */
class ImageWorkshopLayerTest extends \PHPUnit_Framework_TestCase
{
    // Tests
    // ===================================================================================
    
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
     * Test mergeDown
     */
    public function testMergeDown()
    {       
        // Test mergeDown on a sublayer not positionned at level 1
        
        $layer = $this->initializeLayer(2);
        
        $callback = $layer->mergeDown(3);
        
        $layerLevels = $layer->getLayerLevels();
        $layers = $layer->getLayers();
                
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
        );
        
        $this->assertTrue($callback === true, 'Expect $callback to be true (boolean)');
        $this->assertTrue(count($layers) == 3, 'Expect to have 3 registered sublayers');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test mergeDown on a sublayer positionned at level 1
        
        $layer = $this->initializeLayer(2);
        
        $callback = $layer->mergeDown(1);
        
        $layerLevels = $layer->getLayerLevels();
        $layers = $layer->getLayers();
        
        $array = array(
            1 => 2,
            2 => 3,
            3 => 4,
        );
        
        $this->assertTrue($callback === true, 'Expect $callback to be true (boolean)');
        $this->assertTrue(count($layers) == 3, 'Expect to have 3 registered sublayers');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test mergeDown on a non-existing sublayer
        
        $layer = $this->initializeLayer(2);
        
        $callback = $layer->mergeDown(5);
        
        $layerLevels = $layer->getLayerLevels();
        $layers = $layer->getLayers();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($callback === false, 'Expect $callback to be false (boolean)');
        $this->assertTrue(count($layers) == 4, 'Expect to have 4 registered sublayers');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
    }
    
    /**
     * Test mergeAll
     */
    public function testMergeAll()
    {
        $layer = $this->initializeLayer(2);
        
        $layer->mergeAll();
        
        $layerPositions = $layer->getLayerPositions();
        $layerLevels = $layer->getLayerLevels();
        $highestLayerLevel = $layer->getHighestLayerLevel();
        $lastLayerId = $layer->getLastLayerId();
        $layers = $layer->getLayers();
        
        $array = array();
        
        $this->assertEquals($layerPositions, $array, 'Expect $layerPositions to be an empty array');
        $this->assertEquals($layerPositions, $array, 'Expect $layerLevels to be an empty array');
        $this->assertTrue($highestLayerLevel === 0, 'Expect $highestLayerLevel to be 0');
        $this->assertTrue($lastLayerId === 0, 'Expect $lastLayerId to be 0');
        $this->assertTrue(count($layers) == 0, 'Expect to have 0 registered sublayer');
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
        $this->assertTrue($layer->getLayerLevels() == $array, 'Expect $layerLevels to be the array $array');
        
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
        $this->assertTrue($layer->getLayerLevels() == $array, 'Expect $layerLevels to be the array $array');
        
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
        $this->assertTrue($layer->getLayerLevels() == $array, 'Expect $layerLevels to be the array $array');
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
        $this->assertTrue($layer->getLayerLevels() == $array, 'Expect $layerLevels to be the array $array');
        
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
        $this->assertTrue($layer->getLayerLevels() == $array, 'Expect $layerLevels to be the array $array');
        
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
        $this->assertTrue($layer->getLayerLevels() == $array, 'Expect $layerLevels to be the array $array');
    }
    
    /**
     * Test moveTo
     */
    public function testMoveTo()
    {
        // Move on the bottom of a level _____________________
        
        // Test moveTo: move a sublayer on the bottom of the level x + 1 (same position)
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 4, true);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the position at level -10
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, -10, true);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the position at level 0
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 0, true);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
                        
        // Test moveTo: move a sublayer on the bottom of the position at level x - 1
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 2, true);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the position at level x - 2
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(4, 2, true);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 4,
            3 => 2,
            4 => 3,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the first level
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 1, true);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of a higher level
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 5, true);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 4, 'Expect $returnedPosition to be 4');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of a level highest the highest level
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 6, true);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 5,
            5 => 3,
        );
        
        $this->assertTrue($returnedPosition === 5, 'Expect $returnedPosition to be 5');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Move on the top of a level _____________________
        
        // Test moveTo: move a sublayer on the top of the level x + 1
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 4, false);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 4, 'Expect $returnedPosition to be 4');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the bottom of the position at level 0
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 0, false);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of the position at level -10
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, -10, false);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of the position at level x - 1 (same position)
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 2, false);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of the position at level x - 2
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(4, 2, false);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of the highest position
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 5, false);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 5,
            5 => 3,
        );
        
        $this->assertTrue($returnedPosition === 5, 'Expect $returnedPosition to be 5');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of another position
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 1, false);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
            5 => 5,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveTo: move a sublayer on the top of a level highest the highest level
        
        $layer = $this->initializeLayer(3);
        
        $returnedPosition = $layer->moveTo(3, 6, false);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 5,
            5 => 3,
        );
        
        $this->assertTrue($returnedPosition === 5, 'Expect $returnedPosition to be 5');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
    }
    
    /**
     * Test moveUp
     */
    public function testMoveUp()
    {
        // Test moveUp on a sublayer at the level 1
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveUp(1);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 2,
            2 => 1,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveUp on a sublayer not positionned at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveUp(2);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveUp on a the sublayer at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveUp(4);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 4, 'Expect $returnedPosition to be 4');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
    }
    
    /**
     * Test moveDown
     * 
     */
    public function testMoveDown()
    {
        // Test moveDown on a sublayer not positionned at the lowest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveDown(3);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 2, 'Expect $returnedPosition to be 2');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveDown on a sublayer at the level 1
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveDown(1);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($returnedPosition === 1, 'Expect $returnedPosition to be 1');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test moveDown on a sublayer at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $returnedPosition = $layer->moveDown(4);
        
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
            4 => 3,
        );
        
        $this->assertTrue($returnedPosition === 3, 'Expect $returnedPosition to be 3');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
    }
    
    /**
     * Test remove
     */
    public function testRemove()
    {
        // Test remove on an existing sublayer
        
        $layer = $this->initializeLayer(2);
        
        $callback = $layer->remove(3);
        
        $layerLevels = $layer->getLayerLevels();
        $layers = $layer->getLayers();
        $highestLevel = $layer->getHighestLayerLevel();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 4,
        );
        
        $this->assertTrue($callback === true, 'Expect $callback to be true (boolean)');
        $this->assertTrue($highestLevel == 3, 'Expect the highest level to be 3');
        $this->assertTrue(count($layers) == 3, 'Expect to have 3 registered sublayers');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
        
        // Test remove on a non-existing sublayer
        
        $layer = $this->initializeLayer(2);
        
        $callback = $layer->remove(5);
        
        $layers = $layer->getLayers();
        $highestLevel = $layer->getHighestLayerLevel();
        $layerLevels = $layer->getLayerLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
        $this->assertTrue($callback === false, 'Expect $callback to be true (boolean)');
        $this->assertTrue(count($layers) == 4, 'Expect to have 4 registered sublayers');
        $this->assertTrue($highestLevel == 4, 'Expect the highest level to be 4');
        $this->assertTrue($layerLevels == $array, 'Expect $layerLevels to be the array $array');
    }
    
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
    
    // Internals
    // ===================================================================================
    
    /**
     * Initialize a layer
     * 
     * @param integer $method
     */
    protected function initializeLayer($method = 1)
    {
        $layer = ImageWorkshop::initVirginLayer(100, 75);
        
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
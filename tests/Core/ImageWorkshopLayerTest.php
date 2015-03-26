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
    /** @var string */
    protected $workspace = null;

    protected function setUp()
    {
        $this->umask = umask(0);
        $this->workspace = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.time().rand(0, 1000);
        mkdir($this->workspace, 0777, true);
        $this->workspace = realpath($this->workspace);
    }

    protected function tearDown()
    {
        $this->clean($this->workspace);
        umask($this->umask);
    }

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
     * Test resizeInPixel
     */
    public function testResizeInPixel()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(20, 10, true);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 10, 'Expect $layer to have a height of 10px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(20, 10, true, 20, 20, 'MM');
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 10, 'Expect $layer to have a height of 10px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(20, 10, true, 20, 20, 'LB');
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 10, 'Expect $layer to have a height of 10px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(20, 10, true, -20, -20, 'MM');
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 10, 'Expect $layer to have a height of 10px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(20, 10, null);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 10, 'Expect $layer to have a height of 10px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(20, null, null);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(20, null, true);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 15, 'Expect $layer to have a height of 15px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(null, 20, null);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 20, 'Expect $layer to have a height of 20px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(null, 20, true);
        $this->assertTrue($layer->getWidth() == 27, 'Expect $layer to have a width of 27px');
        $this->assertTrue($layer->getHeight() == 20, 'Expect $layer to have a height of 20px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(null, null, null);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(0, 0, null);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPixel(-1, -1, null);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
    }
    
    /**
     * Test resizeInPercent
     */
    public function testResizeInPercent()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(20, 10, true);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 8, 'Expect $layer to have a height of 10px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(20, 10, true, 20, 20, 'MM');
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 8, 'Expect $layer to have a height of 8px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(20, 10, true, 20, 20, 'LB');
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 8, 'Expect $layer to have a height of 8px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(20, 10, true, -20, -20, 'MM');
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 8, 'Expect $layer to have a height of 8px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(20, 10, null);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 8, 'Expect $layer to have a height of 8px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(20, null, null);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(20, null, true);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 15, 'Expect $layer to have a height of 15px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(null, 20, null);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 15, 'Expect $layer to have a height of 15px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(null, 20, true);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 15, 'Expect $layer to have a height of 15px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(null, null, null);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(0, 0, null);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeInPercent(-1, -1, null);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
    }
    
    /**
     * Test resizeToFitInPixel
     */
    public function testResizeToFit()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->resizeToFit(20, 10);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 10, 'Expect $layer to have a height of 10px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeToFit(120, 50);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 50, 'Expect $layer to have a height of 50px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeToFit(60, 100);
        $this->assertTrue($layer->getWidth() == 60, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 50px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeToFit(120, 100);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeToFit(20, 10, true);
        $this->assertTrue($layer->getWidth() == 13, 'Expect $layer to have a width of 13px');
        $this->assertTrue($layer->getHeight() == 10, 'Expect $layer to have a height of 10px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeToFit(20, 18, true);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 15, 'Expect $layer to have a height of 15px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeToFit(120, 100, true);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
    }
    
    /**
     * Test resizeByLargestSideInPixel
     */
    public function testResizeByLargestSideInPixel()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPixel(20, true);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 15, 'Expect $layer to have a height of 15px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPixel(20, false);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPixel(0, true);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPixel(0, false);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPixel(-1, true);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
    }
    
    /**
     * Test resizeByLargestSideInPercent
     */
    public function testResizeByLargestSideInPercent()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPercent(20, true);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 15, 'Expect $layer to have a height of 15px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPercent(20, false);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPercent(0, true);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPercent(0, false);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByLargestSideInPercent(-1, true);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
    }
    
    /**
     * Test resizeByNarrowSideInPixel
     */
    public function testResizeByNarrowSideInPixel()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPixel(20, true);
        $this->assertTrue($layer->getWidth() == 27, 'Expect $layer to have a width of 27px');
        $this->assertTrue($layer->getHeight() == 20, 'Expect $layer to have a height of 20px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPixel(20, false);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 20, 'Expect $layer to have a height of 20px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPixel(0, true);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPixel(0, false);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPixel(-1, true);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
    }
    
    /**
     * Test resizeByNarrowSideInPercent
     */
    public function testResizeByNarrowSideInPercent()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPercent(20, true);
        $this->assertTrue($layer->getWidth() == 20, 'Expect $layer to have a width of 20px');
        $this->assertTrue($layer->getHeight() == 15, 'Expect $layer to have a height of 15px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPercent(20, false);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 15, 'Expect $layer to have a height of 15px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPercent(0, true);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPercent(0, false);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->resizeByNarrowSideInPercent(-1, true);
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
    }
    
    /**
     * Test cropInPixel
     */
    public function testCropInPixel()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPixel(50, 30, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 50, 'Expect $layer to have a width of 50px');
        $this->assertTrue($layer->getHeight() == 30, 'Expect $layer to have a height of 30px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPixel(50, 30, 20, 20, 'LT');
        $this->assertTrue($layer->getWidth() == 50, 'Expect $layer to have a width of 50px');
        $this->assertTrue($layer->getHeight() == 30, 'Expect $layer to have a height of 30px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPixel(50, 30, -20, -20, 'LT');
        $this->assertTrue($layer->getWidth() == 50, 'Expect $layer to have a width of 50px');
        $this->assertTrue($layer->getHeight() == 30, 'Expect $layer to have a height of 30px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPixel(0, 0, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        // Test larger than initial width
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPixel(200, 30, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 200, 'Expect $layer to have a width of 200px');
        $this->assertTrue($layer->getHeight() == 30, 'Expect $layer to have a height of 30px');
        
        // Test negative
        
        $layer = $this->initializeLayer(1);
        
        $this->setExpectedException('PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException');
        $layer->cropInPixel(-1, -1, 0, 0, 'LT');
    }
    
    /**
     * Test cropInPercent
     */
    public function testCropInPercent()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPercent(50, 30, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 50, 'Expect $layer to have a width of 50px');
        $this->assertTrue($layer->getHeight() == 23, 'Expect $layer to have a height of 23px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPercent(50, 30, 20, 20, 'LT');
        $this->assertTrue($layer->getWidth() == 50, 'Expect $layer to have a width of 50px');
        $this->assertTrue($layer->getHeight() == 23, 'Expect $layer to have a height of 23px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPercent(50, 30, -20, -20, 'LT');
        $this->assertTrue($layer->getWidth() == 50, 'Expect $layer to have a width of 50px');
        $this->assertTrue($layer->getHeight() == 23, 'Expect $layer to have a height of 23px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPercent(0, 0, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 1, 'Expect $layer to have a width of 1px');
        $this->assertTrue($layer->getHeight() == 1, 'Expect $layer to have a height of 1px');
        
        // Test larger than initial width
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropInPercent(200, 30, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 200, 'Expect $layer to have a width of 200px');
        $this->assertTrue($layer->getHeight() == 23, 'Expect $layer to have a height of 23px');
        
        // Test negative
        
        $layer = $this->initializeLayer(1);
        
        $this->setExpectedException('PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException');
        $layer->cropInPercent(-1, -1, 0, 0, 'LT');
    }
    
    /**
     * Test cropToAspectRatioInPixel
     */
    public function testCropToAspectRatioInPixel()
    {
        // Test larger width
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPixel(50, 30, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 60, 'Expect $layer to have a height of 60px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPixel(50, 30, 20, 20, 'LT');
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 60, 'Expect $layer to have a height of 60px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPixel(50, 30, -20, -20, 'LT');
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 60, 'Expect $layer to have a height of 60px');
        
        // Test larger than initial width
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPixel(60, 50, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 90, 'Expect $layer to have a width of 90px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPixel(60, 50, 20, 20, 'LT');
        $this->assertTrue($layer->getWidth() == 90, 'Expect $layer to have a width of 90px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPixel(60, 50, -20, -20, 'LT');
        $this->assertTrue($layer->getWidth() == 90, 'Expect $layer to have a width of 90px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPixel(0, 0, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        // Test negative
        
        $layer = $this->initializeLayer(1);
        
        $this->setExpectedException('PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException');
        $layer->cropToAspectRatioInPixel(-1, -1, 0, 0, 'LT');
    }
    
    /**
     * Test cropToAspectRatioInPercent
     */
    public function testCropToAspectRatioInPercent()
    {
        // Test larger width
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPercent(50, 30, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 60, 'Expect $layer to have a height of 60px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPercent(50, 30, 20, 20, 'LT');
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 60, 'Expect $layer to have a height of 60px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPercent(50, 30, -20, -20, 'LT');
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 60, 'Expect $layer to have a height of 60px');
        
        // Test larger than initial width
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPercent(60, 50, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 90, 'Expect $layer to have a width of 90px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPercent(60, 50, 20, 20, 'LT');
        $this->assertTrue($layer->getWidth() == 90, 'Expect $layer to have a width of 90px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPercent(60, 50, -20, -20, 'LT');
        $this->assertTrue($layer->getWidth() == 90, 'Expect $layer to have a width of 90px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropToAspectRatioInPercent(0, 0, 0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        // Test negative
        
        $layer = $this->initializeLayer(1);
        
        $this->setExpectedException('PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException');
        $layer->cropToAspectRatioInPercent(-1, -1, 0, 0, 'LT');
    }
    
    /**
     * Test cropMaximumInPixel
     */
    public function testCropMaximumInPixel()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->cropMaximumInPixel(0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropMaximumInPixel(0, 0, 'MM');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropMaximumInPixel(20, 20, 'LT');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropMaximumInPixel(-20, -20, 'LT');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
    }
    
    /**
     * Test cropMaximumInPercent
     */
    public function testCropMaximumInPercent()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->cropMaximumInPercent(0, 0, 'LT');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropMaximumInPercent(0, 0, 'MM');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropMaximumInPercent(20, 20, 'LT');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->cropMaximumInPercent(-20, -20, 'LT');
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
    }
    
    /**
     * Test rotate
     */
    public function testRotate()
    {
        $layer = $this->initializeLayer(1);
        
        $layer->rotate(0);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->rotate(90);
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 100, 'Expect $layer to have a height of 100px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->rotate(-90);
        $this->assertTrue($layer->getWidth() == 75, 'Expect $layer to have a width of 75px');
        $this->assertTrue($layer->getHeight() == 100, 'Expect $layer to have a height of 100px');
        
        $layer = $this->initializeLayer(1);
        
        $layer->rotate(180);
        $this->assertTrue($layer->getWidth() == 100, 'Expect $layer to have a width of 100px');
        $this->assertTrue($layer->getHeight() == 75, 'Expect $layer to have a height of 75px');
        
        $layer = $this->initializeLayer(1);


        if (version_compare(PHP_VERSION, '5.5', '>=')) {
            // see https://bugs.php.net/bug.php?id=65148
            $this->markTestIncomplete('Disabling some tests while bug #65148 is open');
        }

        $layer->rotate(40);
        $this->assertTrue($layer->getWidth() <= 126 && $layer->getWidth() >= 124, 'Expect $layer to have a width around 125px');
        $this->assertTrue($layer->getHeight() <= 124 && $layer->getHeight() >= 122, 'Expect $layer to have a height around 123px');
    
        $layer = $this->initializeLayer(1);
        
        $layer->rotate(20);
        $this->assertTrue($layer->getWidth() <= 121 && $layer->getWidth() >= 119, 'Expect $layer to have a width around 120px');
        $this->assertTrue($layer->getHeight() <= 107 && $layer->getHeight() >= 105, 'Expect $layer to have a height around 106px');
    
        $layer = $this->initializeLayer(1);
        
        $layer->rotate(-20);
        $this->assertTrue($layer->getWidth() <= 121 && $layer->getWidth() >= 119, 'Expect $layer to have a width around 120px');
        $this->assertTrue($layer->getHeight() <= 107 && $layer->getHeight() >= 105, 'Expect $layer to have a height around 106px');
    }

    public function testSaveWithDirectoryAsFile()
    {
        $destinationFolder = $this->workspace.DIRECTORY_SEPARATOR.'fileDestination';

        $this->setExpectedException(
            'PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException',
            'Destination folder "'.$destinationFolder.'" is a file.',
            6
        );

        touch($destinationFolder);

        $layer = $this->initializeLayer();
        $layer->save($destinationFolder, 'test.png', false);
    }

    public function testSaveWithNonExistDirectory()
    {
        $destinationFolder = $this->workspace.DIRECTORY_SEPARATOR.'nonExistFolder';

        $this->setExpectedException(
            'PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException',
            'Destination folder "'.$destinationFolder.'" not exists.',
            6
        );

        $layer = $this->initializeLayer();
        $layer->save($destinationFolder, 'test.png', false);
    }

    public function testSaveWithNonSupportedFileExtension()
    {
        $this->setExpectedException(
            'PHPImageWorkshop\Core\Exception\ImageWorkshopLayerException',
            'Image format "tif" not supported.',
            7
        );

        $layer = $this->initializeLayer();
        $layer->save($this->workspace, 'test.tif', false);
    }

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

    /**
     * @param string $file
     */
    protected function clean($file)
    {
        if (is_dir($file) && !is_link($file)) {
            $dir = new \FilesystemIterator($file);
            foreach ($dir as $childFile) {
                $this->clean($childFile);
            }
            rmdir($file);
        } else {
            unlink($file);
        }
    }
}

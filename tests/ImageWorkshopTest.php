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
        $layer = new ImageWorkshop(array(
            "width" => 300,
            "height" => 200,
        ));
        
        $this->assertTrue(false);
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
        $layer = $this->initializeLayer(2);
        
        $layer->moveTop(2);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 4,
            4 => 2,
        );
        
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
    }
    
    /**
     * Test moveBottom
     */
    public function testMoveBottom()
    {
        $layer = $this->initializeLayer(2);
        
        $layer->moveBottom(3);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 3,
            2 => 1,
            3 => 2,
            4 => 4,
        );
        
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
    }
    
    /**
     * @todo
     * 
     * Test moveTo
     * 
     */
    /*public function testMoveTo()
    {
        $this->assertTrue(false);
    }*/
    
    /**
     * @todo
     * 
     * Test moveUp
     * 
     */
    public function testMoveUp()
    {
        // Test moveUp on a sublayer not positionned at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $layer->moveUp(2);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
        );
        
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveUp on a the sublayer at the highest level
        
        $layer = $this->initializeLayer(2);
        
        $layer->moveUp(4);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
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
        // Test moveBottom on a sublayer not positionned at the lowest level
        
        $layer = $this->initializeLayer(2);
        
        $layer->moveBottom(3);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 3,
            3 => 2,
            4 => 4,
        );
        
        $this->assertTrue($layersLevels == $array, 'Expect $layersLevels to be the array $array');
        
        // Test moveBottom on a sublayer at the lowest level
        
        $layer = $this->initializeLayer(2);
        
        $layer->moveBottom(1);
        
        $layersLevels = $layer->getLayersLevels();
        
        $array = array(
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
        );
        
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
                
            ;
            
            case 2: // Add 4 sublayers in $layer stack
                
                $layer->addLayer(1, $layer);
                $layer->addLayer(2, $layer);
                $layer->addLayer(3, $layer);
                $layer->addLayer(4, $layer);
                
            ;
        }
        
        return $layer;
    }
}
?>
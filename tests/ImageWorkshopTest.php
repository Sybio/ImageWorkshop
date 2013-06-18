<?php

use PHPImageWorkshop\ImageWorkshop as ImageWorkshop;

require_once(__DIR__.'/autoload.php');

/**
 * ImageWorkshopTest class
 * 
 * Tests ImageWorkshop class
 * 
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain  / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 * 
 */
class ImageWorkshopTest extends \PHPUnit_Framework_TestCase
{
    // Properties
    // ===================================================================================
    
    const IMAGE_SAMPLE_PATH = '/Resources/images/sample1.jpg';
    const FONT_SAMPLE_PATH = '/Resources/fonts/arial.ttf';
    
    // Tests
    // ===================================================================================
    
    /**
     * Test initFromPath
     */
    public function testInitFromPath()
    {
        // test 1
        
        $layer = ImageWorkshop::initFromPath(__DIR__.static::IMAGE_SAMPLE_PATH);
        
        $this->assertTrue(is_object($layer) === true, 'Expect $layer to be an object');
        $this->assertTrue(get_class($layer) === 'PHPImageWorkshop\Core\ImageWorkshopLayer', 'Expect $layer to be an ImageWorkshopLayer object');
    
        // test 2
        
        $this->setExpectedException('PHPImageWorkshop\Exception\ImageWorkshopException');
        $layer = ImageWorkshop::initFromPath('fakePath');
    }
    
    /**
     * Test initTextLayer
     */
    public function testInitTextLayer()
    {
        $layer = ImageWorkshop::initTextLayer('Hello John Doe !', __DIR__.static::FONT_SAMPLE_PATH, 15, 'ff0000', 10, 'ffffff');
        
        $this->assertTrue(is_object($layer) === true, 'Expect $layer to be an object');
        $this->assertTrue(get_class($layer) === 'PHPImageWorkshop\Core\ImageWorkshopLayer', 'Expect $layer to be an ImageWorkshopLayer object');
    }
    
    /**
     * Test initVirginLayer
     */
    public function testInitVirginLayer()
    {
        $layer = ImageWorkshop::initVirginLayer(189, 242, 'ff0000');
        
        $this->assertTrue(is_object($layer) === true, 'Expect $layer to be an object');
        $this->assertTrue(get_class($layer) === 'PHPImageWorkshop\Core\ImageWorkshopLayer', 'Expect $layer to be an ImageWorkshopLayer object');
    }
    
    /**
     * Test initFromResourceVar
     */
    public function testInitFromResourceVar()
    {
        $layer = ImageWorkshop::initFromResourceVar(imageCreateFromJPEG(__DIR__.static::IMAGE_SAMPLE_PATH));
        
        $this->assertTrue(is_object($layer) === true, 'Expect $layer to be an object');
        $this->assertTrue(get_class($layer) === 'PHPImageWorkshop\Core\ImageWorkshopLayer', 'Expect $layer to be an ImageWorkshopLayer object');
    }
    
    /**
     * Test initFromString
     */
    public function testInitFromString()
    {
        $layer = ImageWorkshop::initFromString(file_get_contents(__DIR__.static::IMAGE_SAMPLE_PATH));
        
        $this->assertTrue(is_object($layer) === true, 'Expect $layer to be an object');
        $this->assertTrue(get_class($layer) === 'PHPImageWorkshop\Core\ImageWorkshopLayer', 'Expect $layer to be an ImageWorkshopLayer object');
    }
}
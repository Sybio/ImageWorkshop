<?php

use PHPImageWorkshop\Core\ImageWorkshopLib as ImageWorkshopLib;

require_once(__DIR__.'/../autoload.php');

/**
 * ImageWorkshopLibTest class
 * 
 * Tests ImageWorkshopLib class
 * 
 * @link http://phpimageworkshop.com
 * @author Sybio (Clément Guillemain  / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 * 
 */
class ImageWorkshopLibTest extends \PHPUnit_Framework_TestCase
{
    // Tests
    // ===================================================================================
    
    /**
     * Test convertHexToRGB
     */
    public function testConvertHexToRGB()
    {
        // First test
        $hex = 'ffffff';
        $rgb = ImageWorkshopLib::convertHexToRGB($hex);
        
        $this->assertTrue(getType($rgb) === 'array', 'Expect $rgb to be an array');
        $this->assertTrue((array_key_exists('R', $rgb) && array_key_exists('G', $rgb) && array_key_exists('B', $rgb)) == 3, 'Expect $rgb to have the 3 array keys: "R", "G", and "B"');
        $this->assertTrue($rgb['R'] === 255, 'Expect $rgb["R"] to be an integer of value 255');
        $this->assertTrue($rgb['G'] === 255, 'Expect $rgb["G"] to be an integer of value 255');
        $this->assertTrue($rgb['B'] === 255, 'Expect $rgb["B"] to be an integer of value 255');
        
        // Second test
        $hex = '000000';
        $rgb = ImageWorkshopLib::convertHexToRGB($hex);
        
        $this->assertTrue($rgb['R'] === 0, 'Expect $rgb["R"] to be an integer of value 0');
        $this->assertTrue($rgb['G'] === 0, 'Expect $rgb["G"] to be an integer of value 0');
        $this->assertTrue($rgb['B'] === 0, 'Expect $rgb["B"] to be an integer of value 0');
    }
}
<?php
/**
 * ExifOrientations
 *
 * Constants for EXIF image orientations
 *
 * @link http://phpimageworkshop.com
 * @author Bjørn Børresen | Sybio (Clément Guillemain  / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 */

namespace PHPImageWorkshop\Exif;

/**
 * Container for all EXIF orientations.
 *
 * @package PHPImageWorkshop/Exif
 * @link http://phpimageworkshop.com
 * @author Bjørn Børresen | Sybio (Clément Guillemain  / @Sybio01)
 * @license http://en.wikipedia.org/wiki/MIT_License
 * @copyright Clément Guillemain
 */
final class ExifOrientations
{
    const UNDEFINED    = 0;
    const TOP_LEFT     = 1;
    const TOP_RIGHT    = 2;
    const BOTTOM_RIGHT = 3;
    const BOTTOM_LEFT  = 4;
    const LEFT_TOP     = 5;
    const RIGHT_TOP    = 6;
    const RIGHT_BOTTOM = 7;
    const LEFT_BOTTOM  = 8;
}

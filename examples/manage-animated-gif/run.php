<?php

require __DIR__.'/vendor/autoload.php';

use GifCreator\GifCreator;
use GifFrameExtractor\GifFrameExtractor;
use PHPImageWorkshop\ImageWorkshop;

$gifPath = __DIR__.'/images/original.gif'; // Your animated GIF path
if (GifFrameExtractor::isAnimatedGif($gifPath)) { // check this is an animated GIF

    // Extractions of the GIF frames and their durations
    $gfe = new GifFrameExtractor();
    $frames = $gfe->extract($gifPath);

    // Initialization of the watermark layer
    $watermarkLayer = ImageWorkshop::initFromPath(__DIR__.'/images/watermark.png');

    // For each frame, we add a watermark and we resize it
    $retouchedFrames = array();
    foreach ($frames as $frame) {

        // Initialization of the frame as a layer
        $frameLayer = ImageWorkshop::initFromResourceVar($frame['image']);

        $frameLayer->resizeInPixel(350, null, true); // Resizing
        $frameLayer->addLayerOnTop($watermarkLayer, 20, 20, 'LB'); // Watermarking

        $retouchedFrames[] = $frameLayer->getResult();
    }

    // Then we re-generate the GIF
    $gc = new GifCreator();
    $gc->create($retouchedFrames, $gfe->getFrameDurations(), 0);

    // And now save it !
    file_put_contents(__DIR__.'/output.gif', $gc->getGif());
}

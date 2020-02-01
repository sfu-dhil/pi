<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

use AppBundle\Entity\ScreenShot;
use Imagick;
use ImagickPixel;

/**
 * Description of Thumbnailer
 *
 * @author mjoyce
 */
class Thumbnailer {
    private $thumbWidth;
    
    private $thumbHeight;
    
    public function setThumbWidth($width) {
        $this->thumbWidth = $width;
    }
    
    public function setThumbHeight($height) {
        $this->thumbHeight = $height;
    }
    
    public function thumbnail(ScreenShot $screenShot) {
        $file = $screenShot->getImageFile();
        $thumbname = $file->getBasename('.' . $file->getExtension()) . '_tn.png';
        $magick = new Imagick($file->getPathname());

        $magick->setBackgroundColor(new ImagickPixel('white'));
        $magick->thumbnailimage($this->thumbWidth, $this->thumbHeight, true, true);
        $magick->setImageFormat('png32');

        $handle = fopen($file->getPath() . '/' . $thumbname, 'wb');
        fwrite($handle, $magick->getimageblob());
        
        return $thumbname;
    }
}

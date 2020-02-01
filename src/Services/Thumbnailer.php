<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Services;

use AppBundle\Entity\ScreenShot;
use Imagick;
use ImagickPixel;

/**
 * Description of Thumbnailer.
 *
 * @author mjoyce
 */
class Thumbnailer {
    private $thumbWidth;

    private $thumbHeight;

    public function setThumbWidth($width) : void {
        $this->thumbWidth = $width;
    }

    public function setThumbHeight($height) : void {
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

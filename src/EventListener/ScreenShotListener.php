<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\EventListener;

use App\Entity\ScreenShot;
use App\Services\FileUploader;
use App\Services\Thumbnailer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Description of ScreenShotListener.
 *
 * @author Michael Joyce <ubermichael@gmail.com>
 */
class ScreenShotListener
{
    /**
     * @var FileUploader
     */
    private $uploader;

    /**
     * @var Thumbnailer
     */
    private $thumbnailer;

    private $thumbWidth;

    private $thumbHeight;

    public function __construct(FileUploader $uploader, Thumbnailer $thumbnailer) {
        $this->uploader = $uploader;
        $this->thumbnailer = $thumbnailer;
    }

    private function uploadFile(ScreenShot $screenshot) : void {
        $file = $screenshot->getImageFile();
        if ( ! $file instanceof File) {
            return;
        }
        $filename = $this->uploader->upload($file);
        $screenshot->setImageFilePath($filename);
        $screenshot->setOriginalName($file->getFilename());
        $screenshot->setImageSize($file->getSize());
        $dimensions = getimagesize($this->uploader->getImageDir() . '/' . $filename);
        $screenshot->setImageWidth($dimensions[0]);
        $screenshot->setImageHeight($dimensions[1]);

        $screenshotFile = new File($this->uploader->getImageDir() . '/' . $filename);
        $screenshot->setImageFile($screenshotFile);
        $screenshot->setThumbnailPath($this->thumbnailer->thumbnail($screenshot));
    }

    public function setThumbWidth($width) : void {
        $this->thumbWidth = $width;
    }

    public function setThumbHeight($height) : void {
        $this->thumbHeight = $height;
    }

    public function prePersist(LifecycleEventArgs $args) : void {
        $entity = $args->getEntity();
        if ($entity instanceof ScreenShot) {
            $this->uploadFile($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args) : void {
        $entity = $args->getEntity();
        if ($entity instanceof ScreenShot) {
            $this->uploadFile($entity);
        }
    }

    public function postLoad(LifecycleEventArgs $args) : void {
        $entity = $args->getEntity();
        if ($entity instanceof ScreenShot) {
            $filename = $entity->getImageFilePath();
            if (file_exists($this->uploader->getImageDir() . '/' . $filename)) {
                $file = new File($this->uploader->getImageDir() . '/' . $filename);
                $entity->setImageFile($file);
            }
        }
    }
}

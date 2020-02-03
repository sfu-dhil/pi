<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\EventListener;

use App\Entity\ScreenShot;
use App\Services\FileUploader;
use App\Services\Thumbnailer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of ScreenShotListener
 *
 * @author Michael Joyce <ubermichael@gmail.com>
 */
class ScreenShotListener {

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
    
    public function setThumbWidth($width) {
        $this->thumbWidth = $width;
    }
    
    public function setThumbHeight($height) {
        $this->thumbHeight = $height;
    }
    
    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if($entity instanceof ScreenShot) {
            $this->uploadFile($entity);
        }
    }
    
    public function preUpdate(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if($entity instanceof ScreenShot) {
            $this->uploadFile($entity);
        }
    }
    
    public function postLoad(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if($entity instanceof ScreenShot) {
            $filename = $entity->getImageFilePath();
            if(file_exists($this->uploader->getImageDir() . '/' . $filename)) {
                $file = new File($this->uploader->getImageDir() . '/' . $filename);
                $entity->setImageFile($file);
            }
        }
    }
    
    private function uploadFile(ScreenShot $screenshot) {
        $file = $screenshot->getImageFile();
        if( ! $file instanceof UploadedFile) {
            return;
        }
        $filename = $this->uploader->upload($file);        
        $screenshot->setImageFilePath($filename);
        $screenshot->setOriginalName($file->getClientOriginalName());
        $screenshot->setImageSize($file->getClientSize());
        $dimensions = getimagesize($this->uploader->getImageDir() . '/' . $filename);
        $screenshot->setImageWidth($dimensions[0]);
        $screenshot->setImageHeight($dimensions[1]);
        
        $screenshotFile = new File($this->uploader->getImageDir() . '/' . $filename);
        $screenshot->setImageFile($screenshotFile);
        $screenshot->setThumbnailPath($this->thumbnailer->thumbnail($screenshot));
    }
    
}

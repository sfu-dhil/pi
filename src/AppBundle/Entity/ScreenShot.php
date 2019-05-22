<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;
use Symfony\Component\HttpFoundation\File\File;

/**
 * ScreenShot
 * @ORM\Table(name="screen_shot")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ScreenShotRepository")
 */
class ScreenShot extends AbstractEntity
{

    /**
     * @var string
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $originalName;

    /**
     * @var File
     */
    private $imageFile;

    /**
     * @var string
     * @ORM\Column(type="string", length=48, nullable=false)
     */
    private $imageFilePath;

    /**
     * @var string
     * @ORM\Column(type="string", length=48, nullable=false)
     */
    private $thumbnailPath;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $imageSize;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $imageWidth;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $imageHeight;

    /**
     * @var Video
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="screenShots")
     */
    private $video;

    public function __toString()
    {
        return $this->originalName;
    }


    /**
     * Set originalName.
     *
     * @param string $originalName
     *
     * @return ScreenShot
     */
    public function setOriginalName($originalName)
    {
        $this->originalName = $originalName;

        return $this;
    }

    /**
     * Get originalName.
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->originalName;
    }

    /**
     * Get the image file
     *
     * @return File
     */
    public function getImageFile() {
        return $this->imageFile;
    }

    /**
     * @param File $imageFile
     *
     * @return $this
     */
    public function setImageFile(File $imageFile) {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * Set imageFilePath.
     *
     * @param string $imageFilePath
     *
     * @return ScreenShot
     */
    public function setImageFilePath($imageFilePath)
    {
        $this->imageFilePath = $imageFilePath;

        return $this;
    }

    /**
     * Get imageFilePath.
     *
     * @return string
     */
    public function getImageFilePath()
    {
        return $this->imageFilePath;
    }

    /**
     * Set thumbnailPath.
     *
     * @param string $thumbnailPath
     *
     * @return ScreenShot
     */
    public function setThumbnailPath($thumbnailPath)
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

    /**
     * Get thumbnailPath.
     *
     * @return string
     */
    public function getThumbnailPath()
    {
        return $this->thumbnailPath;
    }

    /**
     * Set imageSize.
     *
     * @param int $imageSize
     *
     * @return ScreenShot
     */
    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    /**
     * Get imageSize.
     *
     * @return int
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }

    /**
     * Set imageWidth.
     *
     * @param int|null $imageWidth
     *
     * @return ScreenShot
     */
    public function setImageWidth($imageWidth = null)
    {
        $this->imageWidth = $imageWidth;

        return $this;
    }

    /**
     * Get imageWidth.
     *
     * @return int|null
     */
    public function getImageWidth()
    {
        return $this->imageWidth;
    }

    /**
     * Set imageHeight.
     *
     * @param int|null $imageHeight
     *
     * @return ScreenShot
     */
    public function setImageHeight($imageHeight = null)
    {
        $this->imageHeight = $imageHeight;

        return $this;
    }

    /**
     * Get imageHeight.
     *
     * @return int|null
     */
    public function getImageHeight()
    {
        return $this->imageHeight;
    }

    /**
     * Set video.
     *
     * @param \AppBundle\Entity\Video|null $video
     *
     * @return ScreenShot
     */
    public function setVideo(\AppBundle\Entity\Video $video = null)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video.
     *
     * @return \AppBundle\Entity\Video|null
     */
    public function getVideo()
    {
        return $this->video;
    }
}

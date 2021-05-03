<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Video;
use App\Services\FileUploader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ScreenShotType form.
 */
class ScreenShotType extends AbstractType {
    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(FileUploader $fileUploader) {
        $this->fileUploader = $fileUploader;
    }

    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('video', EntityType::class, [
            'class' => Video::class,
            'choice_label' => 'title',
            'disabled' => true,
        ]);

        $builder->add('imageFile', FileType::class, [
            'label' => 'Clipping Image',
            'required' => true,
            'attr' => [
                'help_block' => "Select a file to upload which is less than {$this->fileUploader->getMaxUploadSize(false)} in size.",
                'data-maxsize' => $this->fileUploader->getMaxUploadSize(),
            ],
        ]);
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\ScreenShot',
        ]);
    }
}

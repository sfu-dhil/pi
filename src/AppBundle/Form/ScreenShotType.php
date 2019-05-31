<?php

namespace AppBundle\Form;

use AppBundle\Entity\Video;
use AppBundle\Services\FileUploader;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * ScreenShotType form.
 */
class ScreenShotType extends AbstractType
{

    /**
     * @var FileUploader
     */
    private $fileUploader;

    public function __construct(FileUploader $fileUploader) {
        $this->fileUploader = $fileUploader;
    }

    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('imageFile', FileType::class, array(
            'label' => 'Clipping Image',
            'required' => true,
            'attr' => array(
                'help_block' => "Select a file to upload which is less than {$this->fileUploader->getMaxUploadSize(false)} in size.",
                'data-maxsize' => $this->fileUploader->getMaxUploadSize(),
            ),
        ));

        $builder->add('video', Select2EntityType::class, array(
            'multiple' => false,
            'remote_route' => 'video_typeahead',
            'class' => Video::class,
            'primary_key' => 'id',
            'page_limit' => 10,
            'allow_clear' => true,
            'delay' => 250,
            'language' => 'en',
            'required' => true,
        ));
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ScreenShot'
        ));
    }

}

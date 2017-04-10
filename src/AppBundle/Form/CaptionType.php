<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaptionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {    
        $builder->add('youtubeId');     
        $builder->add('etag');     
        $builder->add('lastUpdated');     
        $builder->add('trackKind');     
        $builder->add('language');     
        $builder->add('name');     
        $builder->add('audioTrackType');     
        $builder->add('isCC');     
        $builder->add('isDraft');     
        $builder->add('isAutoSynced');     
        $builder->add('content');     
        $builder->add('video');         
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Caption'
        ));
    }
}

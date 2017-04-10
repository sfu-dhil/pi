<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {    
        $builder->add('youtubeId');     
        $builder->add('etag');     
        $builder->add('publishedAt');     
        $builder->add('title');     
        $builder->add('description');     
        $builder->add('thumbnail');     
        $builder->add('duration');     
        $builder->add('definition');     
        $builder->add('caption');     
        $builder->add('license');     
        $builder->add('embeddable');     
        $builder->add('viewCount');     
        $builder->add('likeCount');     
        $builder->add('dislikeCount');     
        $builder->add('favouriteCount');     
        $builder->add('commentCount');     
        $builder->add('player');     
        $builder->add('channel');     
        $builder->add('keywords');     
        $builder->add('playlists');         
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Video'
        ));
    }
}

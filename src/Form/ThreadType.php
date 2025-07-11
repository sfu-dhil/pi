<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThreadType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('replyCount');
        $builder->add('youtubeId');
        $builder->add('etag');
        $builder->add('refreshed');
        $builder->add('channel');
        $builder->add('video');
        $builder->add('root');
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Thread',
        ]);
    }
}

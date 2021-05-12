<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CaptionType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
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

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'App\Entity\Caption',
        ]);
    }
}

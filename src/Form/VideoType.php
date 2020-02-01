<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
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

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Video',
        ]);
    }
}

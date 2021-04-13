<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\ProfileKeyword;
use App\Entity\VideoProfile;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $profileElements = $options['profile_elements'];
        $profile = $options['profile'];

        foreach ($profileElements as $profileElement) {
            $name = $profileElement->getName();
            $builder->add($name, EntityType::class, [
                'class' => ProfileKeyword::class,
                'choice_label' => 'label',
                'choice_value' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => [
                    'class' => 'selectable',
                    'data-element-name' => $profileElement->getName(),
                    'help_block' => $profileElement->getDescription(),
                ],
                'query_builder' => function (EntityRepository $er) use ($profileElement) {
                    $qb = $er->createQueryBuilder('pk');
                    $qb->andWhere('pk.profileElement = :pe');
                    $qb->setParameter('pe', $profileElement);
                    $qb->orderBy('pk.label');

                    return $qb;
                },
                'data' => $profile->getProfileKeywords($profileElement),
                'mapped' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => VideoProfile::class,
            'profile_elements' => [],
            'profile' => null,
        ]);
    }
}

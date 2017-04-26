<?php

namespace AppBundle\Form;

use AppBundle\Form\DataTransformer\TagsTransformer;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoProfileType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $profileElements = $options['profile_elements'];
        foreach ($profileElements as $profileElement) {
            $name = $profileElement->getName();            
            $builder->add($name, EntityType::class, array(
                'class' => 'AppBundle:ProfileKeyword',
                'choice_label' => 'label',
                'choice_value' => 'name',
                'multiple' => true,
                'expanded' => false,
                'required' => false,
                'attr' => array(
                    'class' => 'selectable'
                ),
                'query_builder' => function(EntityRepository $er) use ($profileElement) {
                    $qb = $er->createQueryBuilder('pk');
                    $qb->andWhere('pk.profileElement = :pe');
                    $qb->setParameter('pe', $profileElement);
                    $qb->orderBy('pk.label');
                    return $qb;
                }
            ));
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => null,
            'profile_elements' => array(),
        ));
    }

}

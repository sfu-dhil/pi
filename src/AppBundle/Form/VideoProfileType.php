<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            $builder->add($profileElement->getName(), ChoiceType::class, array(
                'label' => $profileElement->getLabel(),
                'choices' => $profileElement->getProfileKeywords(),
                'choice_label' => function($value, $key, $index) {
                    return $value->getLabel();
                },
                'choice_value' => function($value) {
                    return $value->getName();
                },
                'data' => [],
                'required' => false,
                'expanded' => false,
                'multiple' => true,
                'attr' => array(
                    'class' => 'selectable',
                    'data-element-name' => $profileElement->getName(),
                )
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

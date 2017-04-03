<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class to build some menus for navigation.
 */
class Builder implements ContainerAwareInterface {

    use ContainerAwareTrait;

    /**
     * Build a menu for blog posts.
     * 
     * @param FactoryInterface $factory
     * @param array $options
     * @return ItemInterface
     */
    public function navMenu(FactoryInterface $factory, array $options) {
        $em = $this->container->get('doctrine')->getManager();

        $menu = $factory->createItem('root');
        $menu->setChildrenAttributes(array(
            'class' => 'dropdown-menu',
        ));
        $menu->setAttribute('dropdown', true);

//        $menu->addChild('artwork', array(
//            'label' => 'Artworks',
//            'route' => 'artwork_index',
//        ));
        
        return $menu;
    }

}

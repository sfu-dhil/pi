<?php

namespace AppBundle\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class to build some menus for navigation.
 */
class Builder implements ContainerAwareInterface {

    use ContainerAwareTrait;

    const CARET = ' ▾'; // U+25BE, black down-pointing small triangle.
    
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $authChecker, TokenStorageInterface $tokenStorage) {
        $this->factory = $factory;
        $this->authChecker = $authChecker;
        $this->tokenStorage = $tokenStorage;
    }

    private function hasRole($role) {
        if (!$this->tokenStorage->getToken()) {
            return false;
        }
        return $this->authChecker->isGranted($role);
    }

    /**
     * Build a menu for blog posts.
     * 
     * @param array $options
     * @return ItemInterface
     */
    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes(array(
            'class' => 'nav navbar-nav',
        ));
        
        $menu->addChild('home', array(
            'label' => 'Home',
            'route' => 'homepage',
        ));
        
        if( ! $this->hasRole('ROLE_USER')) {
            return $menu;
        }
        
        $menu->addChild('browse', array(
            'uri' => '#',
            'label' => 'Browse ' . self::CARET,
        ));
        $browse = $menu['browse']->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');
        
        $browse->addChild('playlist', array(
            'label' => 'Playlists',
            'route' => 'playlist_index',
        ));

        $browse->addChild('video', array(
            'label' => 'Videos',
            'route' => 'video_index',
        ));

        $browse->addChild('channel', array(
            'label' => 'Channels',
            'route' => 'channel_index',
        ));

        $browse->addChild('caption', array(
            'label' => 'Captions',
            'route' => 'caption_index',
        ));
        
        if ($this->hasRole('ROLE_USER')) {
            $browse->addChild('divider', array(
                'label' => '',
            ));
            $browse['divider']->setAttributes(array(
                'role' => 'separator',
                'class' => 'divider',
            ));
            $browse->addChild('keyword', array(
                'label' => 'Video Keywords',
                'route' => 'keyword_index',
            ));
            $browse->addChild('profile_element', array(
                'label' => 'Profile Elements',
                'route' => 'profile_element_index',
            ));
            $browse->addChild('profile_keyword', array(
                'label' => 'Profile Keywords',
                'route' => 'profile_keyword_index',
            ));
            $browse->addChild('video_profiles', array(
                'label' => 'Video Profiles',
                'route' => 'video_profile_index',
            ));
            $browse->addChild('screen_shots', array(
                'label' => 'Screen Shots',
                'route' => 'screen_shot_index',
            ));
        }
        
        return $menu;
    }

}

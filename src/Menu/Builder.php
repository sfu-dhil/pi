<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Menu;

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

    public const CARET = ' ▾'; // U+25BE, black down-pointing small triangle.

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
        if ( ! $this->tokenStorage->getToken()) {
            return false;
        }

        return $this->authChecker->isGranted($role);
    }

    /**
     * Build a menu for blog posts.
     *
     * @return ItemInterface
     */
    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes([
            'class' => 'nav navbar-nav',
        ]);

        $menu->addChild('home', [
            'label' => 'Home',
            'route' => 'homepage',
        ]);

        if ( ! $this->hasRole('ROLE_USER')) {
            return $menu;
        }

        $menu->addChild('browse', [
            'uri' => '#',
            'label' => 'Browse ' . self::CARET,
        ]);
        $browse = $menu['browse']->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');

        $browse->addChild('playlist', [
            'label' => 'Playlists',
            'route' => 'playlist_index',
        ]);

        $browse->addChild('video', [
            'label' => 'Videos',
            'route' => 'video_index',
        ]);

        $browse->addChild('caption', [
            'label' => 'Captions',
            'route' => 'caption_index',
        ]);
        $browse->addChild('figuration', [
            'label' => 'Figurations',
            'route' => 'figuration_index',
        ]);

        if ($this->hasRole('ROLE_USER')) {
            $browse->addChild('divider', [
                'label' => '',
            ]);
            $browse['divider']->setAttributes([
                'role' => 'separator',
                'class' => 'divider',
            ]);
            $browse->addChild('keyword', [
                'label' => 'Video Keywords',
                'route' => 'keyword_index',
            ]);
            $browse->addChild('profile_element', [
                'label' => 'Profile Elements',
                'route' => 'profile_element_index',
            ]);
            $browse->addChild('profile_keyword', [
                'label' => 'Profile Keywords',
                'route' => 'profile_keyword_index',
            ]);
            $browse->addChild('video_profiles', [
                'label' => 'Video Profiles',
                'route' => 'video_profile_index',
            ]);
            $browse->addChild('screen_shots', [
                'label' => 'Screen Shots',
                'route' => 'screen_shot_index',
            ]);
        }

        return $menu;
    }
}

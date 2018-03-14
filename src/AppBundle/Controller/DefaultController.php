<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Video;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/")
 */
class DefaultController extends Controller {

    /**
     * @Route("", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request) {
        $user = $this->getUser();
        if (!$user) {
            return array(
                'unprofiledVideos' => array(),
            );
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $query = $repo->findVideosWithoutProfile($user);
        $paginator = $this->get('knp_paginator');
        $unprofiledVideos = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'unprofiledVideos' => $unprofiledVideos,
        );
    }

    /**
     * @Route("oauth2callback", name="oauth2callback")
     * @param Request $request
     * @Security("has_role('ROLE_USER')")
     */
    public function oauthCallbackAction(Request $request) {
        $client = $this->get('yt.client')->getClient();
        $client->authenticate($request->query->get('code'));
        $access_token = $client->getAccessToken();

        $user = $this->getUser();
        $user->setData(AppBundle::AUTH_USER_KEY, $access_token);
        $this->getDoctrine()->getManager()->flush($user);
        $this->addFlash('success', "Access to YouTube is granted");
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("oauth2request", name="oauth2request")
     * @param Request $request
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     */
    public function requestAuth(Request $request) {
        $client = $this->get('yt.client')->getClient();
        return $this->redirect($client->createAuthUrl());
    }

    /**
     * @Route("oauth2revoke", name="oauth2revoke")
     * @param Request $request
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @return type
     */
    public function revokeAuth(Request $request) {
        $client = $this->get('yt.client')->getClient();
        $client->revokeToken();
        $user = $this->getUser();
        $user->setData(AppBundle::AUTH_USER_KEY, null);
        $this->getDoctrine()->getManager()->flush($user);
        $this->addFlash('success', 'Access to YouTube is revoked.');
        return $this->redirectToRoute('homepage');
    }

}

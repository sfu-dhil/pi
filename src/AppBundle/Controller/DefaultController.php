<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use AppBundle\Entity\Video;
use AppBundle\Services\YoutubeClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{

    /**
     * @Route("", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $user = $this->getUser();
        $paginator = $this->get('knp_paginator');
        $query = array();
        if ($user) {
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository(Video::class);
            $query = $repo->findVideosWithoutProfile($user);
        }
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
    public function oauthCallbackAction(Request $request, YoutubeClient $client)
    {
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
    public function requestAuth(Request $request, YoutubeClient $client)
    {
        return $this->redirect($client->createAuthUrl());
    }

    /**
     * @Route("oauth2revoke", name="oauth2revoke")
     * @param Request $request
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     *
     * @return RedirectResponse
     */
    public function revokeAuth(Request $request, YoutubeClient $client)
    {
        $client->revokeToken();
        $user = $this->getUser();
        $user->setData(AppBundle::AUTH_USER_KEY, null);
        $this->getDoctrine()->getManager()->flush($user);
        $this->addFlash('success', 'Access to YouTube is revoked.');
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/privacy", name="privacy")
     * @Template()
     */
    public function privacyAction(Request $request)
    {
    }

}

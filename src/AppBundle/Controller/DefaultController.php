<?php

namespace AppBundle\Controller;

use AppBundle\AppBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/")
 */
class DefaultController extends Controller {

    /**
     * @Route("", name="homepage")
     * @Template()
     */
    public function indexAction(Request $request) {
        return array();
    }

    /**
     * @Route("oauth2callback", name="oauth2callback")
     * @param Request $request
     */
    public function oauthCallbackAction(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_USER');        
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
     */
    public function requestAuth(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $client = $this->get('yt.client')->getClient();
        return $this->redirect($client->createAuthUrl());
    }
    
    /**
     * @Route("oauth2revoke", name="oauth2revoke")
     * @param Request $request
     * @return type
     */
    public function revokeAuth(Request $request) {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $client = $this->get('yt.client')->getClient();
        $client->revokeToken();
        $user = $this->getUser();
        $user->setData(AppBundle::AUTH_USER_KEY, null);
        $this->getDoctrine()->getManager()->flush($user);
        $this->addFlash('success', 'Access to YouTube is revoked.');
        return $this->redirectToRoute('homepage');
    }

}

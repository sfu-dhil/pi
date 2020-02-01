<?php

namespace App\Controller;

use App\App;
use App\Entity\Video;
use App\Services\YoutubeClient;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController
{

    /**
     * @Route("", name="homepage", methods={"GET"})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array(
        );
    }

    /**
     * @Route("/privacy", name="privacy", methods={"GET"})
     * @Template()
     */
    public function privacyAction(Request $request)
    {
    }

}

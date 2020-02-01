<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefaultController extends AbstractController {
    /**
     * @Route("", name="homepage", methods={"GET"})
     * @Template()
     */
    public function indexAction(Request $request) {
        return [
        ];
    }

    /**
     * @Route("/privacy", name="privacy", methods={"GET"})
     * @Template()
     */
    public function privacyAction(Request $request) : void {
    }
}

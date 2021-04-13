<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Caption;
use App\Repository\CaptionRepository;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Caption controller.
 *
 * @Route("/caption")
 */
class CaptionController extends AbstractController implements PaginatorAwareInterface
{
    use PaginatorTrait;

    /**
     * Lists all Caption entities.
     *
     * @Route("/", name="caption_index")
     *
     * @Template
     *
     * @return array
     */
    public function indexAction(Request $request, CaptionRepository $repo) {
        $query = $repo->findCaptionsQuery($this->getUser());
        $captions = $this->paginator->paginate($query, $request->query->getint('page', 1), 20);

        return [
            'captions' => $captions,
        ];
    }

    /**
     * Finds and displays a Caption entity.
     *
     * @Route("/{id}", name="caption_show")
     *
     * @Template
     *
     * @return array
     */
    public function showAction(Caption $caption) {
        if (null === $this->getuser() && $caption->getVideo()->getHidden()) {
            throw new NotFoundHttpException();
        }

        return [
            'caption' => $caption,
        ];
    }
}

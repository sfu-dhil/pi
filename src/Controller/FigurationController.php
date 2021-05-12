<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Figuration;
use App\Entity\Video;
use App\Form\FigurationType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Figuration controller.
 *
 * @Route("/figuration")
 */
class FigurationController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Figuration entities.
     *
     * @return array
     * @Route("/", name="figuration_index", methods={"GET"})
     * @Template
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Figuration::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $figurations = $this->paginator->paginate($query, $request->query->getint('page', 1), 20);

        return [
            'figurations' => $figurations,
            'repo' => $em->getRepository(Figuration::class),
        ];
    }

    /**
     * Creates a new Figuration entity.
     *
     * @return array|RedirectResponse
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="figuration_new", methods={"GET", "POST"})
     * @Template
     */
    public function newAction(Request $request) {
        $figuration = new Figuration();
        $form = $this->createForm(FigurationType::class, $figuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($figuration);
            $em->flush();

            $this->addFlash('success', 'The new figuration was created.');

            return $this->redirectToRoute('figuration_show', ['id' => $figuration->getId()]);
        }

        return [
            'figuration' => $figuration,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Figuration entity and paginates it.
     *
     * @return array
     * @Route("/{id}", name="figuration_show", methods={"GET"})
     * @Template
     */
    public function showAction(Request $request, Figuration $figuration) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $query = $repo->findVideosQuery($this->getUser(), [
            'type' => Figuration::class,
            'id' => $figuration->getId(),
        ]);
        $videos = $this->paginator->paginate($query, $request->query->getint('page', 1), 20);

        return [
            'figuration' => $figuration,
            'videos' => $videos,
        ];
    }

    /**
     * Displays a form to edit an existing Figuration entity.
     *
     * @return array|RedirectResponse
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="figuration_edit", methods={"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Figuration $figuration) {
        $editForm = $this->createForm(FigurationType::class, $figuration);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The figuration has been updated.');

            return $this->redirectToRoute('figuration_show', ['id' => $figuration->getId()]);
        }

        return [
            'figuration' => $figuration,
            'edit_form' => $editForm->createView(),
        ];
    }
}

<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\ProfileElement;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ProfileElement controller.
 *
 * @Route("/profile_element")
 */
class ProfileElementController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all ProfileElement entities.
     *
     * @Route("/", name="profile_element_index", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $dql = 'SELECT e FROM App:ProfileElement e ORDER BY e.id';
        $query = $em->createQuery($dql);

        $profileElements = $this->paginator->paginate($query, $request->query->getint('page', 1), 20);

        return [
            'profileElements' => $profileElements,
        ];
    }

    /**
     * Finds and displays a ProfileElement entity.
     *
     * @Route("/{id}", name="profile_element_show", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function showAction(ProfileElement $profileElement) {
        return [
            'profileElement' => $profileElement,
        ];
    }
}

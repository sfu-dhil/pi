<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\ProfileKeyword;
use App\Entity\VideoProfile;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ProfileKeyword controller.
 *
 * @Route("/profile_keyword")
 */
class ProfileKeywordController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all ProfileKeyword entities.
     *
     * @Route("/", name="profile_keyword_index", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $dql = 'SELECT e FROM App:ProfileKeyword e ORDER BY e.id';
        $query = $em->createQuery($dql);

        $profileKeywords = $this->paginator->paginate($query, $request->query->getint('page', 1), 20);

        return [
            'profileKeywords' => $profileKeywords,
        ];
    }

    /**
     * Finds and displays a ProfileKeyword entity.
     *
     * @Route("/{id}", name="profile_keyword_show", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function showAction(Request $request, ProfileKeyword $profileKeyword, VideoRepository $repo) {
        $query = $repo->findVideosQuery($this->getUser(), [
            'type' => VideoProfile::class,
            'id' => $profileKeyword->getId(),
        ]);
        $videos = $this->paginator->paginate($query, $request->query->getint('page', 1), 20);

        return [
            'profileKeyword' => $profileKeyword,
            'videos' => $videos,
        ];
    }
}

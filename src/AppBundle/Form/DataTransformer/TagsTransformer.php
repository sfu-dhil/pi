<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\ProfileKeyword;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Description of TagsTransformer
 *
 * @author mjoyce
 */
class TagsTransformer implements DataTransformerInterface {
    
    /**
     * @var ObjectManager
     */
    private $em;
    
    public function __construct(ObjectManager $em) {
        $this->em = $em;
    }
    
    public function transform($tags) {
        return $tags;
    }

    /**
     * @param Collection|ProfileKeyword[] $tags
     * @return Collection
     */
    public function reverseTransform($tags) {
        dump(["reversetransform:", $tags]);
        return $tags;
//        $collection = new ArrayCollection();
//        $repo = $this->em->getRepository(ProfileKeyword::class);
//        foreach($tags as $tag) {
//            $entity = $repo->findOneBy(array('name' => $tag->getName()));
//            if( ! $entity) {
//                $collection->add($tag);
//            } else {
//                $collection->add($entity);
//            }
//        }
//        return $collection;
    }

}

<?php

namespace AppBundle\Command;

use AppBundle\Services\YoutubeClient;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Nines\UserBundle\Entity\User;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Description of AbstractCommand
 *
 * @author michael
 */
abstract class AbstractCmd extends ContainerAwareCommand {
    
    /**
     * @var ObjectManager
     */
    protected $em;
    
    /**
     * @var YoutubeClient
     */
    protected $client;
    
    protected function configure() {
        $this->addArgument('user', InputArgument::REQUIRED, 'Authorized username for Youtube.');
        $this->addOption('all', null, InputOption::VALUE_NONE, 'Refresh all playlists');
    }

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->client = $container->get('yt.client');
    }
    
    protected function setUser($username) {
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(array('email' => $username));
        if( ! $user) {
            throw new RuntimeException("Uknown user {$username}.");
        }
        $this->client->setUser($user);
    }
    
    /**
     * @return Collection
     */
    protected function getEntities($class, $all) {
        $repo = $this->em->getRepository($class);
        if($all) {
            return $repo->findAll();
        }
        return $repo->findBy(array('refreshed' => null));
    }
    
}

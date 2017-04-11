<?php

namespace AppBundle\Command;

use AppBundle\Entity\Video;
use AppBundle\Services\YoutubeClient;
use Doctrine\Common\Persistence\ObjectManager;
use Nines\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppRefreshVideosCommand extends ContainerAwareCommand
{

    /**
     * @var ObjectManager
     */
    private $em;
    
    /**
     * @var YoutubeClient
     */
    private $client;
    
    protected function configure() {
        $this->setName('app:refresh:videos');
        $this->addArgument('user', InputArgument::REQUIRED, 'Authorized username for Youtube.');
        $this->addOption('all', null, InputOption::VALUE_NONE, 'Refresh all playlists');
    }

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->client = $container->get('yt.client');
    }
    
    public function getVideos($all) {
        $repo = $this->em->getRepository(Video::class);
        if($all) {
            return $repo->findAll();
        } else {
            return $repo->findBy(array('refreshed' => null));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $username = $input->getArgument('user');
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(array('email' => $username));
        if( ! $user) {
            throw new RuntimeException("Uknown user {$username}.");
        }
        $this->client->setUser($user);
        $all = $input->getOption('all');
        $videos = $this->getVideos($all);        
        $this->client->updateVideos($videos);
        $this->em->flush();
    }

}

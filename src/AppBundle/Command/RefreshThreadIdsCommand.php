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

class RefreshThreadIdsCommand extends ContainerAwareCommand {

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var YoutubeClient
     */
    private $client;

    protected function configure() {
        $this->setName('app:refresh:threadIds');
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
        return $repo->findAll();
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $username = $input->getArgument('user');
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(array('email' => $username));
        if (!$user) {
            throw new RuntimeException("Uknown user {$username}.");
        }
        $this->client->setUser($user);
        $all = $input->getOption('all');
        $videos = $this->getVideos($all);
        foreach ($videos as $video) {
            $output->writeln($video->getYoutubeId());
            try {
                $this->client->updateThreadIds($video);
                sleep(rand(1, 5));
            } catch (\Google_Service_Exception $e) {
                $errors = $e->getErrors();
                $output->writeln("Cannot update thread IDs for {$video->getYoutubeId()}");
                $output->writeln($errors[0]['message']);
            }
        }
        $this->em->flush();
    }

}

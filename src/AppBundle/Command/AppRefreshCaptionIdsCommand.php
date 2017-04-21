<?php

namespace AppBundle\Command;

use AppBundle\Entity\Caption;
use AppBundle\Entity\Video;
use AppBundle\Services\YoutubeClient;
use Doctrine\Common\Persistence\ObjectManager;
use Google_Service_Exception;
use Nines\UserBundle\Entity\User;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppRefreshCaptionIdsCommand extends ContainerAwareCommand {

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var YoutubeClient
     */
    private $client;

    protected function configure() {
        $this->setName('app:refresh:captionIds');
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
        if ($all) {
            return $repo->findAll();
        } else {
            return $repo->findBy(array('refreshed' => null));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $username = $input->getArgument('user');
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(array('email' => $username));
        if (!$user) {
            throw new RuntimeException("Uknown user {$username}.");
        }
        $this->client->setUser($user);
        $all = $input->getOption('all');
        $videos = $this->getVideos($all);

        $captionRepo = $this->em->getRepository(Caption::class);

        foreach ($videos as $video) {
            $oldIds = $video->getCaptionIds()->toArray();
            try {
                $ids = $this->client->captionIds($video);
                foreach (array_diff($ids, $oldIds) as $newId) {
                    $caption = new Caption();
                    $caption->setYoutubeId($newId);
                    $caption->setVideo($video);
                    $video->addCaption($caption);
                    $this->em->persist($caption);
                    $this->em->flush();
                }
                foreach (array_diff($oldIds, $ids) as $oldId) {
                    $caption = $captionRepo->findOneBy(array('youtubeId' => $oldId));
                    if (!$caption) {
                        continue;
                    }
                    $video->removeCaption($caption);
                    $this->em->flush();
                }
            } catch (Google_Service_Exception $e) {
                $output->writeln("Cannot get captions for {$video->getYoutubeId()}.");
                foreach($e->getErrors() as $error) {
                    $output->writeln($error['message']);
                }
                continue;
            }
        }
    }

}

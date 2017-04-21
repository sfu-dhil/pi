<?php

namespace AppBundle\Command;

use AppBundle\Entity\Caption;
use AppBundle\Services\YoutubeClient;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Exception\DriverException;
use Nines\UserBundle\Entity\User;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppRefreshCaptionsCommand extends ContainerAwareCommand {

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var YoutubeClient
     */
    private $client;

    protected function configure() {
        $this->setName('app:refresh:captions');
        $this->addArgument('user', InputArgument::REQUIRED, 'Authorized username for Youtube.');
        $this->addOption('all', null, InputOption::VALUE_NONE, 'Refresh all playlists');
    }

    public function setContainer(ContainerInterface $container = null) {
        parent::setContainer($container);
        $this->em = $container->get('doctrine')->getManager();
        $this->client = $container->get('yt.client');
    }

    public function getCaptions($all) {
        $repo = $this->em->getRepository(Caption::class);
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
        $captions = $this->getCaptions($all);

        foreach ($captions as $caption) {
            $output->writeln("Updating {$caption->getYoutubeId()}");
            $this->client->updateCaption($caption);
            $this->em->flush();
            sleep(rand(0,5));
        }
    }

}

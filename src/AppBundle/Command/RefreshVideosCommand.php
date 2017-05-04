<?php

namespace AppBundle\Command;

use AppBundle\Entity\Video;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshVideosCommand extends AbstractCmd {

    protected function configure() {
        parent::configure();
        $this->setName('app:refresh:videos');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->setUser($input->getArgument('user'));
        $videos = $this->getEntities(Video::class, $input->getOption('all'));
        $this->client->updateVideos($videos);
        $this->em->flush();
    }

}

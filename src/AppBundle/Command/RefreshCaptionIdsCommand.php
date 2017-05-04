<?php

namespace AppBundle\Command;

use AppBundle\Entity\Video;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCaptionIdsCommand extends AbstractCmd {

    protected function configure() {
        parent::configure();
        $this->setName('app:refresh:captionIds');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->setUser($input->getArgument('user'));
        $videos = $this->getEntities(Video::class, true);
        foreach($videos as $video) {
            $output->writeln("Caption Ids for: {$video->getYoutubeId()}", OutputInterface::VERBOSITY_VERBOSE);
            $this->client->captionIds($video);
        }
        $this->em->flush();
    }

}

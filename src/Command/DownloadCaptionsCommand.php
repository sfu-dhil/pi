<?php

namespace App\Command;

use App\Entity\Caption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCaptionsCommand extends AbstractCmd {

    protected function configure() {
        parent::configure();
        $this->setName('app:download:captions');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->setUser($input->getArgument('user'));
        $captions = $this->getEntities(Caption::class, $input->getOption('all'));
        foreach($captions as $caption) {
            $output->writeln("{$caption->getYoutubeId()}", OutputInterface::VERBOSITY_VERBOSE);
            $this->client->updateCaption($caption);
            $this->em->flush();
        }
    }

}

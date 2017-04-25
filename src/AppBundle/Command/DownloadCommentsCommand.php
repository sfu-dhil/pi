<?php

namespace AppBundle\Command;

use AppBundle\Entity\Comment;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCommentsCommand extends AbstractCmd {

    protected function configure() {
        parent::configure();
        $this->setName('app:download:comments');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->setUser($input->getArgument('user'));
        $comments = $this->getEntities(Comment::class, $input->getOption('all'));
        foreach($comments as $comment) {
            $output->writeln("{$comment->getYoutubeId()}", OutputInterface::VERBOSITY_VERBOSE);
            $this->client->updateComments($comment);
            $this->em->flush();
        }
    }

}

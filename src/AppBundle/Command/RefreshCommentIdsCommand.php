<?php

namespace AppBundle\Command;

use AppBundle\Entity\Thread;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshCommentIdsCommand extends AbstractCmd {

    protected function configure() {
        parent::configure();
        $this->setName('app:refresh:commentIds');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->setUser($input->getArgument('user'));
        $threads = $this->getEntities(Thread::class, true);
        foreach($threads as $thread) {
            $output->writeln("thread: {$thread->getYoutubeId()}", OutputInterface::VERBOSITY_VERBOSE);
            $this->client->updateCommentIds($thread);
        }
        $this->em->flush();
    }

}

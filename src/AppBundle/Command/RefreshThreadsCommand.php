<?php

namespace AppBundle\Command;

use AppBundle\Entity\Thread;
use AppBundle\Entity\Video;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshThreadsCommand extends AbstractCmd {

    protected function configure() {
        parent::configure();
        $this->setName('app:refresh:threads');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->setUser($input->getArgument('user'));
        $videos = $this->getEntities(Video::class, true);
        foreach ($videos as $video) {
            $output->writeln("video: {$video->getYoutubeId()}", OutputInterface::VERBOSITY_VERBOSE);
            $this->client->updateThreadIds($video);
        }
        $this->em->flush();
        $threads = $this->getEntities(Thread::class, $input->getOption('all'));
        // @todo batching here.
        $this->client->updateThreads($threads);
        foreach($threads as $thread) {
            $output->writeln("thread: {$thread->getYoutubeId()}", OutputInterface::VERBOSITY_VERBOSE);
            $this->client->updateCommentIds($thread);
            $this->em->flush();
        }        
    }

}

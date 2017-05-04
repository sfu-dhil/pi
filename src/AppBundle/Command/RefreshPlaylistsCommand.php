<?php

namespace AppBundle\Command;

use AppBundle\Entity\Playlist;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshPlaylistsCommand extends AbstractCmd {

    protected function configure() {
        parent::configure();
        $this->setName('app:refresh:playlists');
        $this->addOption('videos', NULL, InputOption::VALUE_NONE, 'Update playlist video IDs.');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->setUser($input->getArgument('user'));
        $playlists = $this->getEntities(Playlist::class, $input->getOption('all'));
        $this->client->updatePlaylists($playlists);

        if ($input->getOption('videos')) {
            foreach ($playlists as $playlist) {
                $output->writeln("VideoIds for {$playlist->getYoutubeId()}", OutputInterface::VERBOSITY_VERBOSE);                
                $this->client->playlistVideos($playlist);
            }
        }

        $this->em->flush();
    }

}

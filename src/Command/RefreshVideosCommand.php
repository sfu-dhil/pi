<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Video;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshVideosCommand extends AbstractCmd
{
    protected function configure() : void {
        parent::configure();
        $this->setName('app:refresh:videos');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->setUser($input->getArgument('user'));
        $videos = $this->getEntities(Video::class, $input->getOption('all'));
        $this->client->updateVideos($videos);
        $this->em->flush();
    }
}

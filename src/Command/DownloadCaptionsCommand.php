<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Caption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DownloadCaptionsCommand extends AbstractCmd {
    protected function configure() : void {
        parent::configure();
        $this->setName('app:download:captions');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->setUser($input->getArgument('user'));
        $captions = $this->getEntities(Caption::class, $input->getOption('all'));

        foreach ($captions as $caption) {
            $output->writeln("{$caption->getYoutubeId()}", OutputInterface::VERBOSITY_VERBOSE);
            $this->client->updateCaption($caption);
            $this->em->flush();
        }
    }
}

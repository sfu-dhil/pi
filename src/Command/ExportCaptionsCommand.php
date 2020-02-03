<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Caption;
use App\Entity\Video;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCaptionsCommand extends Command {
    public const PATTERN = '/^\\d[0-9,:.> -]*$/';

    protected function configure() : void {
        $this->setName('app:export:captions');
        $this->setDescription('Export captions data to files.');
        $this->addArgument('path', InputArgument::REQUIRED, 'Path to the export file.');
    }

    protected function clean(Caption $caption) {
        $lines = preg_split('/\n|\r\n?/', $caption->getContent());
        foreach ($lines as &$line) {
            $line = preg_replace(self::PATTERN, '', $line);
        }

        return implode("\n", array_filter($lines));
    }

    protected function export($path, Video $video) : void {
        foreach ($video->getCaptions() as $caption) {
            if ('en' !== substr($caption->getLanguage(), 0, 2)) {
                continue;
            }
            if ( ! $caption->getContent()) {
                continue;
            }
            $fh = fopen("{$path}/{$video->getId()}.{$caption->getTrackKind()}.txt", 'w');
            fwrite($fh, $this->clean($caption));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void {
        $path = $input->getArgument('path');
        $em = $this->getContainer()->get('doctrine');
        $videos = $em->getRepository(Video::class)->findAll();
        if ( ! file_exists($path)) {
            mkdir($path);
        }
        foreach ($videos as $video) {
            if ( ! $video->getCaptionsAvailable() || ! $video->getCaptionsDownloadable()) {
                continue;
            }
            $this->export($path, $video);
        }
    }
}

<?php

namespace AppBundle\Command;

use AppBundle\Entity\Caption;
use AppBundle\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCaptionsCommand extends ContainerAwareCommand
{    
    const PATTERN = "/^\d[0-9,:.> -]*$/";
    
    protected function configure()
    {
        $this->setName('app:export:captions');
        $this->setDescription('Export captions data to files.');
        $this->addArgument('path', InputArgument::REQUIRED, null);
    }
    
    protected function clean(Caption $caption) {
        $lines = preg_split('/\n|\r\n?/', $caption->getContent());
        foreach($lines as &$line) {
            $line = preg_replace(self::PATTERN, '', $line);
        }
        dump($lines);
        return implode("\n", array_filter($lines));
    }
    
    protected function export($path, Video $video) {
        foreach($video->getCaptions() as $caption) {
            if( substr($caption->getLanguage(), 0, 2) !== 'en') {
                continue;
            }
            if( ! $caption->getContent()) {
                continue;
            }
            $fh = fopen("{$path}/{$video->getId()}.{$caption->getTrackKind()}.txt", 'w');
            fwrite($fh, $this->clean($caption));
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $em = $this->getContainer()->get('doctrine');
        $videos = $em->getRepository(Video::class)->findAll();
        if( !file_exists($path)) {
            mkdir($path);
        }
        foreach($videos as $video) {
            if( ! $video->getCaptionsAvailable() || !$video->getCaptionsDownloadable()) {
                continue;
            }
            $this->export($path, $video);
        }
    }

}

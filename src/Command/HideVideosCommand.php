<?php

namespace AppBundle\Command;

use AppBundle\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AppHideVideosCommand command.
 */
class HideVideosCommand extends ContainerAwareCommand
{
    const BATCH_SIZE = 100;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        parent::__construct();
        $this->em = $em;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName('app:hide:videos')
            ->addArgument('files', InputArgument::IS_ARRAY, 'List of files with video IDs to hide.')
        ;
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     *   Command input, as defined in the configure() method.
     * @param OutputInterface $output
     *   Output destination.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $input->getArgument('files');
        $i = 0;
        foreach($files as $file) {
            $output->writeln($file);
            $fh = fopen($file, 'r');
            while(($line = fgets($fh))) {
                $i++;
                /** @var Video $video */
                $video = $this->em->find(Video::class, $line);
                $video->setHidden(true);
                if($i % self::BATCH_SIZE) {
                    $this->em->flush();
                    $this->em->clear();
                }
            }
        }
        $this->em->flush();
        $this->em->clear();
    }

}

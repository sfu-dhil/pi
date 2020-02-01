<?php

namespace App\Command;

use App\Entity\ScreenShot;
use App\Services\Thumbnailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PiRegenerateThumbnailsCommand command.
 */
class RegenerateThumbnailsCommand extends ContainerAwareCommand
{

    /**
     * @var Thumbnailer
     */
    private $thumbnailer;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(Thumbnailer $thumbnailer, EntityManagerInterface $em, $name = null) {
        parent::__construct($name);
        $this->thumbnailer = $thumbnailer;
        $this->em = $em;
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            ->setName('pi:regenerate:thumbnails')
            ->setDescription('Regenerate all the thumbnails')
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
        $repo = $this->em->getRepository(ScreenShot::class);
        foreach($repo->findAll() as $screenshot) {
            /** @var ScreenShot $screenshot */
            $this->thumbnailer->thumbnail($screenshot);
            $output->writeln($screenshot->getImageFilePath() . ' saved to ' . $screenshot->getThumbnailPath());
        }
    }

}

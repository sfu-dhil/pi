<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ScreenShot;
use App\Services\Thumbnailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * PiRegenerateThumbnailsCommand command.
 */
class RegenerateThumbnailsCommand extends Command {
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
    protected function configure() : void {
        $this
            ->setName('pi:regenerate:thumbnails')
            ->setDescription('Regenerate all the thumbnails')
        ;
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     *                              Command input, as defined in the configure() method.
     * @param OutputInterface $output
     *                                Output destination.
     */
    protected function execute(InputInterface $input, OutputInterface $output) : void {
        $repo = $this->em->getRepository(ScreenShot::class);

        foreach ($repo->findAll() as $screenshot) {
            // @var ScreenShot $screenshot
            $this->thumbnailer->thumbnail($screenshot);
            $output->writeln($screenshot->getImageFilePath() . ' saved to ' . $screenshot->getThumbnailPath());
        }
    }
}

<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Figuration;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import Figurations from a CSV file.
 */
class ImportFigurationsCommand extends ContainerAwareCommand {
    /**
     * Database interface.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ImportFigurationsCommand constructor.
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct();
    }

    /**
     * Configure the command.
     */
    protected function configure() : void {
        $this->setName('pi:import:figurations')
            ->setDescription('Import figurations from a CSV file.')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the file to import.')
            ->addOption('skip', null, InputOption::VALUE_REQUIRED, 'Lines to skip during import', 1)
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
        $file = $input->getArgument('file');
        $skip = $input->getOption('skip');

        $handle = fopen($file, 'r');
        $i = 0;
        if ($skip) {
            for ($i = 0; $i < $skip; $i++) {
                $i++;
                fgetcsv($handle);
            }
        }
        $figRepo = $this->em->getRepository(Figuration::class);
        while ($row = fgetcsv($handle)) {
            $i++;
            if ( ! $row[1]) {
                continue;
            }
            /** @var Video $video */
            $video = $this->em->find(Video::class, $row[0]);
            $label = trim($row[1]);
            $figuration = $figRepo->findOneBy(['label' => $label]);
            if ( ! $figuration) {
                $output->writeln('NEW FIGURATION ' . $label);
                $figuration = new Figuration();
                $figuration->setLabel($label);
                $figuration->setName(mb_convert_case($label, MB_CASE_LOWER));
                $this->em->persist($figuration);
            }
            $video->setFiguration($figuration);
//            dump([$i, $row, $video->getId(), $figuration->getId()]);
            $this->em->flush();
            $this->em->clear();
        }
    }
}

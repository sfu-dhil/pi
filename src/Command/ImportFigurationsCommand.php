<?php

namespace AppBundle\Command;

use AppBundle\Entity\Figuration;
use AppBundle\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Import Figurations from a CSV file.
 */
class ImportFigurationsCommand extends ContainerAwareCommand
{

    /**
     * Database interface.
     *
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * ImportFigurationsCommand constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct();
    }

    /**
     * Configure the command.
     */
    protected function configure() {
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
     *   Command input, as defined in the configure() method.
     * @param OutputInterface $output
     *   Output destination.
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        $file = $input->getArgument('file');
        $skip = $input->getOption('skip');

        $handle = fopen($file, 'r');
        $i = 0;
        if($skip) {
            for($i = 0; $i < $skip; $i++) {
                $i++;
                fgetcsv($handle);
            }
        }
        $figRepo = $this->em->getRepository(Figuration::class);
        while($row = fgetcsv($handle)) {
            $i++;
            if(! $row[1]) {
                continue;
            }
            /** @var Video $video */
            $video = $this->em->find(Video::class, $row[0]);
            $label = trim($row[1]);
            $figuration = $figRepo->findOneBy(array('label' => $label));
            if(! $figuration) {
                $output->writeln("NEW FIGURATION " . $label);
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

<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ProfileKeyword;
use App\Entity\ScreenShot;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

class ImportScreenshotsCommand extends Command {
    public const TX = [
        'Celebrity (m & f)' => 'Celebrity',
        'Celebrity (m) & nameless (f) model' => 'Celebrity',
        'celebrity & celebrity (m)' => 'Celebrity',
        'celebrity (f & m) ' => 'Celebrity',
        'celebrity (f&m)' => 'Celebrity',
        'celebrity (m&f)' => 'Celebrity',
        'female celebrity ' => 'Celebrity (f)',
        'female celebrity' => 'Celebrity (f)',
        'male celebrity ' => 'Celebrity (m)',
        'male celebrity and female celebrity ' => 'Celebrity',
        'male celebrity' => 'Celebrity (m)',
        'nameless  female model and nameless male model' => 'nameless model',
        'nameless (f & m) model' => 'nameless model',
        'nameless (f&m) model and animal' => 'nameless model',
        'nameless (f&m) model' => 'nameless model',
        'nameless (f) model and animal' => 'nameless (f) model',
        'nameless (f) model_' => 'nameless (f) model',
        'nameless (f) models' => 'nameless (f_ model',
        'nameless (m & f) model' => 'nameless model',
        'nameless (m and f) model' => 'nameless model',
        'nameless (m&f) model' => 'nameless model',
        'nameless (m) model & celebrity (f)' => 'nameless (m) model',
        'nameless female (1) model' => 'nameless (f) model',
        'nameless female (2) model' => 'nameless (f) model',
        'nameless female model ' => 'nameless (f) model',
        'nameless female model and nameless male model' => 'nameless model',
        'nameless female model' => 'nameless (f) model',
        'nameless male model ' => 'nameless (m) model',
        'nameless male model and nameless female model ' => 'nameless model',
        'nameless male model' => 'nameless (m) model',
        'nameless male modle' => 'nameless (m) model',
        'namless (f & m) model' => 'nameless model',
        'namless (f and m) model' => 'nameless model',
        'namless (m) model' => 'nameless (m) model',
        'photoshop  male geek' => 'Photoshop (m) geek',
        'photoshop female geek ' => 'Photoshop (f) geek',
        'photoshop female geek' => 'Photoshop (f) geek',
        'photoshop male geek ' => 'Photoshop (m) geek',
        'photoshop male geek' => 'Photoshop (m) geek',
        'nameless (f_ model' => 'nameless (f) model',
    ];

    /**
     * @var EntityManagerInterface
     */
    private $em;

    protected static $defaultName = 'app:import:screenshots';

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct(self::$defaultName);
    }

    protected function configure() : void {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('path', InputArgument::REQUIRED, 'Path to the screenshot folder')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $dir = rtrim($input->getArgument('path'), '/');
        $finder = new Finder();
        $finder->ignoreUnreadableDirs()
            ->files()
            ->in($dir)
        ;

        $keywordRepo = $this->em->getRepository(ProfileKeyword::class);
        $videoRepo = $this->em->getRepository(Video::class);

        foreach ($finder as $file) {
            $path = $file->getRealPath();
            $output->writeln($path);
            if ( ! preg_match("|^{$dir}/\\d+|", $path)) {
                continue;
            }
            $basename = basename($path);
            $matches = [];
            if ( ! preg_match('/^(\d+)\s*_\s*(.*?)\.png$/', $basename, $matches)) {
                $output->writeln('Cannot parse file name. Skipped.');

                continue;
            }
            $videoId = $matches[1];

            $key = preg_replace('/\s*\(?\d+\)?\s*$/', '', $matches[2]);
            if (array_key_exists($key, self::TX)) {
                $key = self::TX[$key];
            }
            $keyword = $keywordRepo->findOneBy([
                'name' => $key,
            ]);
            if ( ! $keyword) {
                $output->writeln("Unknown keyword '{$keyword}'. Skipped.");

                continue;
            }

            $video = $videoRepo->find($videoId);

            $screenShot = new ScreenShot();
            $screenShot->setVideo($video);
            $screenShot->setProfileKeyword($keyword);
            $screenShot->setImageFile(new File($path));
            $this->em->persist($screenShot);
            $this->em->flush();
            $this->em->clear();
        }

        return 0;
    }
}

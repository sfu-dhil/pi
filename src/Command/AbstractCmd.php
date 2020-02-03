<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Services\YoutubeClient;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Nines\UserBundle\Entity\User;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Description of AbstractCommand.
 *
 * @author michael
 */
abstract class AbstractCmd extends ContainerAwareCommand {
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var YoutubeClient
     */
    protected $client;

    public function __construct(EntityManagerInterface $em, YoutubeClient $client) {
        parent::__construct();
        $this->em = $em;
        $this->client = $client;
    }

    protected function configure() : void {
        $this->addArgument('user', InputArgument::REQUIRED, 'Authorized username for Youtube.');
        $this->addOption('all', null, InputOption::VALUE_NONE, 'Refresh all playlists');
    }

    protected function setUser($username) : void {
        $userRepo = $this->em->getRepository(User::class);
        $user = $userRepo->findOneBy(['email' => $username]);
        if ( ! $user) {
            throw new RuntimeException("Uknown user {$username}.");
        }
        $this->client->setUser($user);
    }

    /**
     * @param mixed $class
     * @param mixed $all
     *
     * @return Collection|object[]
     */
    protected function getEntities($class, $all) {
        $repo = $this->em->getRepository($class);
        if ($all) {
            return $repo->findAll();
        }

        return $repo->findBy(['refreshed' => null]);
    }
}

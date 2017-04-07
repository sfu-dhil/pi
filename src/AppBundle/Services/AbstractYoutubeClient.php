<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

use Google_Client;
use Google_Service_YouTube;
use Monolog\Logger;

/**
 * Description of AbstractYoutubeClient
 *
 * @author michael
 */
abstract class AbstractYoutubeClient {

    private $youtubeKey;

    /**
     * @var Logger
     */
    protected $logger;
    private $youtubeClient;

    public function __construct($youtubeKey) {
        $this->youtubeClient = null;
        $this->youtubeKey = $youtubeKey;
    }

    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * @return Google_Service_YouTube
     */
    protected function getClient() {
        if (!$this->youtubeClient) {
            $googleClient = new Google_Client();
            $googleClient->setDeveloperKey($this->youtubeKey);
            $this->youtubeClient = new Google_Service_YouTube($googleClient);
        }
        return $this->youtubeClient;
    }

}

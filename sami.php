<?php

use Sami\Sami;
use Sami\RemoteRepository\GitHubRemoteRepository;
use Symfony\Component\Finder\Finder;

$dir = __DIR__;

$config = array(
    'title'                => 'PI API',
    'build_dir'            => $dir . '/public/docs/api',
    'cache_dir'            => $dir . '/var/cache/sami',
    'default_opened_level' => 2,
);

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in('src');

return new Sami($iterator, $config);

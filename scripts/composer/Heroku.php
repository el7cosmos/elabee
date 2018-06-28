<?php

namespace DrupalProject\composer;

use Composer\Script\Event;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;

class Heroku {

  public static function pgPassFile(Event $event) {
    $fs = new Filesystem();
    $finder = new DrupalFinder();
    $finder->locateRoot(getcwd());
    $root = $finder->getComposerRoot();

    if (!$fs->exists($root . '/.pgpass') && $database_url = getenv('DATABASE_URL')) {
      $connection = parse_url($database_url);
      $pass = implode(':', [
        $connection['host'],
        $connection['port'],
        basename($connection['path']),
        $connection['user'],
        $connection['pass'],
      ]);
      $fs->dumpFile($root . '/.pgpass', $pass);
      $fs->chmod($root . '/.pgpass', 0600);

      $event->getIO()->write('Create PostgreSQL password file');
    }
  }

}

<?php

namespace Drush;

use Drush\Commands\DrushCommands;

class HerokuCommands extends DrushCommands {

  /**
   * Heroku release.
   *
   * @command heroku:release
   */
  public function release() {
    drush_invoke_process('@self', 'config-split:import');
    drush_invoke_process('@self', 'cache:rebuild');
  }

}

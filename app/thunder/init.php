<?php

if (file_exists(FCPATH . 'config.php')) {
    require_once FCPATH . 'config.php';
    require_once FCPATH . 'app/core/functions.php';
    require_once FCPATH . 'app/core/Database.php';
    require_once FCPATH . 'app/models/Migration.php';
}
// No config.php yet - only reachable via `do:install` (the `thunder` script
// itself refuses any other command in this state). doInstall() loads
// functions.php/Database.php/Migration.php itself once it has actually
// written a real config.php.

require_once FCPATH . 'app/thunder/thunder.php';
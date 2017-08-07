<?php

// The zazzle file is important for all the module folders of zazzle. 
// It holds the includes for the module

namespace zazzle;

require_once("core/access.php");
require_once("core/core.php");
require_once("core/registry.php");
require_once("classes/hook.class.php");

use zazzle\core\Config;
use zazzle\core\Access;

//Runs the config!!!
Config::read_config();
Access::register_access("index", "^(.*)^", "index.php?dir=$1", ['%{REQUEST_URI}' => ['!^/static/']]);

Config::config();


use zazzle\core\registry\Registry;
Registry::get(new classes\Hook("Test"));




// other modules load?

?>
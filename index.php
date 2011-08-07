<?php

ini_set("display_errors", "On");
error_reporting(E_ALL);
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
require dirname(__FILE__) . '/core/core.php';
owp_core::core();
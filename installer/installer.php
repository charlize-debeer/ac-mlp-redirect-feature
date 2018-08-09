#!/usr/bin/env php
<?php

/**
 * @todo migrate this to an execuatable, ala wp!
 */

require dirname( __DIR__ ) . '/vendor/autoload.php';
require __DIR__ . '/commands/class-install-command.php';

use AC_Plugin_Boilerplate\commands\Install_Command;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add( new Install_Command );

try {
	$application->run();
} catch ( \Exception $e ) {
	var_dump( $e->getMessage() );
}


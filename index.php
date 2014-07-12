<?php
$loader = require 'vendor/autoload.php';
$loader->setUseIncludePath(true);
class_alias('Luracast\\Restler\\Restler', 'Restler');

use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Assets');
$r->addAPIClass('Schedules');
$r->addAPIClass('SubSchedules', '');
$r->addAPIClass('Users');
$r->addAuthenticationClass('Auth');
$r->handle();


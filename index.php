<?php
$loader = require 'vendor/autoload.php';
$loader->setUseIncludePath(true);
class_alias('Luracast\\Restler\\Restler', 'Restler');

use Luracast\Restler\Restler;

error_log(implode("|", get_declared_classes()), 3, "/tmp/php_error.log");

$r = new Restler();
$r->addAPIClass('Assets');
$r->addAPIClass('Schedules');
$r->addAPIClass('SubSchedules', '');
$r->addAPIClass('Users');
$r->addAuthenticationClass('Auth');
$r->handle();


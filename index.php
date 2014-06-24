<?php
require_once 'vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Assets');
$r->addAPIClass('Schedules');
$r->addAPIClass('SubSchedules', '');
$r->addAPIClass('Users');
$r->addAuthenticationClass('Auth');
$r->handle();


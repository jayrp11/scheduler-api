<?php
require_once 'vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Auth', '');
$r->addAPIClass('Assets');
$r->addAPIClass('Schedules');
$r->addAPIClass('SubSchedules', '');
$r->addAuthenticationClass('SimpleAuth');
$r->handle();


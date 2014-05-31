<?php
require_once 'vendor/restler.php';
use Luracast\Restler\Restler;

$r = new Restler();
$r->addAPIClass('Resources');
$r->addAPIClass('Schedules');
$r->handle();


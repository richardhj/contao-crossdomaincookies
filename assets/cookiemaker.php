<?php

require '../../system/initialize.php';

$cookieMaker = new Richardhj\Contao\CrossDomainCookies\CookieMaker();
$cookieMaker->handle();

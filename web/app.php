<?php

require __DIR__ . "/../vendor/autoload.php";
require __DIR__ . '/../src/bootstrap.php';

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

Debug::enable();

$kernel = require __DIR__ . '/../src/bootstrap.php';

// run
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

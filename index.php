<?php

require_once "vendor/autoload.php";

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

use Saboohy\Limiter\RateLimiter;

$limiter = new RateLimiter();

$limiter->clientIp("127.0.0.1");

$limiter->addRoute(method: "GET", path: "/", limit: 5, second: 10);
$limiter->addRoute(method: "GET", path: "/about", limit: 2, second: 10);
$limiter->addRoute(method: "GET", path: "/contact", limit: 3, second: 10);
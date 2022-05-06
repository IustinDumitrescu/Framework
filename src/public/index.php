<?php

ini_set('memory_limit', '512M');

require __DIR__ . '/../vendor/autoload.php';

use App\Kernel;
use App\Http\Request;
use App\Session\Session;

(new Session())->start();

if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
}


if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}

$kernel = new Kernel();

$run = $kernel->run();

if ($run && $run !== 'notadmin') {
    print_r($run);
} else if ($run === 'notadmin') {
    header('Location: /',true, 301);
} else {
    header('HTTP/1.1 404 NOT FOUND');
}






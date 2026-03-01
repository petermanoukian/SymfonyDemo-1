<?php
// config/routes/public/auth/login.php

use App\Controller\Public\LoginController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    
    // Define the Login logic here
    $routes->add('app_login', '/login')
        ->controller([LoginController::class, 'login'])
        ->methods(['GET', 'POST']);

    // Define the Logout logic here
    $routes->add('app_logout', '/logout')
        ->methods(['GET']);
};
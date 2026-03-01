<?php

use App\Controller\SuperAdmin\SuperAdminController; // Messiah: Clean Import
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('app_superadmin_index', '/superadmin')
        ->controller([SuperAdminController::class, 'index']); // Clean & Disciplined

     $routes->import('cats.php');  
     $routes->import('subcats.php');      
};
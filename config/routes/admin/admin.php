<?php

use App\Controller\Admin\AdminController; // Messiah: Clean Import
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->add('app_admin_index', '/admin')
        ->controller([AdminController::class, 'index']); // Clean & Disciplined
};
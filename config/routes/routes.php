<?php
// config/routes/routes.php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Controller\SystemController;

return function (RoutingConfigurator $routes): void {



    
    // We "include" the auth routes by importing the specific file
    $routes->import('public/auth/login.php');

    // 2. Admin Territory (ROLE_ADMIN)
    $routes->import('admin/admin.php');

    // 3. Super Admin Territory (ROLE_SUPER_ADMIN)
    $routes->import('super/superadmin.php');

    $routes->add('cache_clear', '/system/cache-clear')
            ->controller([SystemController::class, 'clearCache'])
            ->methods(['GET']);




};



<?php

use App\Controller\SuperAdmin\CatController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    


    $routes->add('app_superadmin_cat_index', '/superadmin/cat')
        ->controller([CatController::class, 'index'])
        ->methods(['GET']);

    // THE DATA: The JSON engine for DataTables
    $routes->add('app_superadmin_cat_data', '/superadmin/cat/ajaxdata')
        ->controller([CatController::class, 'indexAjax'])
        ->methods(['GET', 'POST']); // DataTables usually uses POST for server-side




    // 2. THE ADD VIEW (GET) - Returns the form (Perfect for Modal or Page)
    $routes->add('app_superadmin_cat_create', '/superadmin/cat/new')
        ->controller([CatController::class, 'create'])
        ->methods(['GET']);

// 8. CHECK NAME EXISTENCE (GET/POST) - Sophisticated validation
    // Optional {id} for "Ignore ID" logic during updates
    $routes->add('app_superadmin_cat_check_name', '/superadmin/cat/check-name/{id}')
        ->controller([CatController::class, 'checkName'])
        ->methods(['GET', 'POST'])
        ->defaults(['id' => null])
        ->requirements(['id' => '\d+']); // Ensures ID is always a number if present


    $routes->add('app_superadmin_cat_edit', '/superadmin/cat/edit/{id}')
        ->controller([CatController::class, 'edit'])
        ->methods(['GET']);


    // 3. THE ADD ACTION (POST) - Processes the new entry
    $routes->add('app_superadmin_cat_store', '/superadmin/cat/store')
        ->controller([CatController::class, 'store'])
        ->methods(['POST']);

    // 4. THE EDIT VIEW (GET) - Pre-fills the form for a specific ID


    // 5. THE UPDATE ACTION (POST/PUT) - Processes the changes
    $routes->add('app_superadmin_cat_update', '/superadmin/cat/update/{id}')
        ->controller([CatController::class, 'update'])
        ->methods(['POST', 'PUT']);



    // 6. DELETE SINGLE
    $routes->add('app_superadmin_cat_delete', '/superadmin/cat/delete/{id}')
        ->controller([CatController::class, 'delete'])
        ->methods(['POST', 'DELETE']);

    // 7. DELETE MANY (Bulk Action)
    $routes->add('app_superadmin_cat_delete_many', '/superadmin/cat/delete-many')
        ->controller([CatController::class, 'deleteMany'])
        ->methods(['POST']);  

};
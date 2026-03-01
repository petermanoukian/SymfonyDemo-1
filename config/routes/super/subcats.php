<?php

use App\Controller\SuperAdmin\SubcatController;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {

    // 1. THE LIST VIEW - Added /{catid}
    $routes->add('app_superadmin_subcat_index', '/superadmin/subcat/{catid}')
        ->controller([SubcatController::class, 'index'])
        ->methods(['GET'])
        ->defaults(['catid' => null])
        ->requirements(['catid' => '\d+']);

    // 2. THE DATA ENGINE - Added /{catid}
    $routes->add('app_superadmin_subcat_data', '/superadmin/subcat/ajaxdata/{catid}')
        ->controller([SubcatController::class, 'indexAjax'])
        ->methods(['GET', 'POST'])
        ->defaults(['catid' => null])
        ->requirements(['catid' => '\d+']);

    // THE DROPDOWN SELECTOR (Unchanged, already had it)
    $routes->add('app_superadmin_subcat_selector', '/api/subcat/selector/{catId}')
        ->controller([SubcatController::class, 'selector'])
        ->methods(['GET', 'POST'])
        ->defaults(['catId' => null]);

    // 3. CREATE - Added /{catid}
    $routes->add('app_superadmin_subcat_create', '/superadmin/subcat/new/{catid}')
        ->controller([SubcatController::class, 'create'])
        ->methods(['GET'])
        ->defaults(['catid' => null])
        ->requirements(['catid' => '\d+']);

    // --- EVERYTHING BELOW REMAINS EXACTLY AS YOU HAD IT ---

    $routes->add('app_superadmin_subcat_store', '/superadmin/subcat/store')
        ->controller([SubcatController::class, 'store'])
        ->methods(['POST']);

    $routes->add('app_superadmin_subcat_edit', '/superadmin/subcat/edit/{id}')
        ->controller([SubcatController::class, 'edit'])
        ->methods(['GET'])
        ->requirements(['id' => '\d+']);

    $routes->add('app_superadmin_subcat_update', '/superadmin/subcat/update/{id}')
        ->controller([SubcatController::class, 'update'])
        ->methods(['POST', 'PUT'])
        ->requirements(['id' => '\d+']);

    $routes->add('app_superadmin_subcat_delete', '/superadmin/subcat/delete/{id}')
        ->controller([SubcatController::class, 'delete'])
        ->methods(['POST', 'DELETE'])
        ->requirements(['id' => '\d+']);

    $routes->add('app_superadmin_subcat_delete_many', '/superadmin/subcat/delete-many')
        ->controller([SubcatController::class, 'deleteMany'])
        ->methods(['POST', 'DELETE']);

    $routes->add('app_superadmin_subcat_check_name', '/superadmin/subcat/check-name/{id}')
        ->controller([SubcatController::class, 'checkName'])
        ->methods(['GET', 'POST'])
        ->defaults(['id' => null]);
};
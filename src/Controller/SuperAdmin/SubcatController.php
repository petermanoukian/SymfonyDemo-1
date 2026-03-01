<?php

namespace App\Controller\SuperAdmin;

use App\Entity\Subcat;
use App\Form\SubcatType;
use App\Service\SubcatService;
use App\Service\CatService;
use App\Service\ImageUploadService;
use App\Service\FileUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Response;

class SubcatController extends AbstractController
{
    public function __construct(
        private SubcatService $subcatService,
        private CatService $catService,
        private ImageUploadService $imageService,
        private FileUploaderService $fileService
    ) {}

    /**
     * View for Subcategories. 
     * Optional 'catid' can be passed via Query String to pre-filter the table.
     */
// src/Controller/SuperAdmin/SubcatController.php

    public function index(Request $request, ?int $catid = null): Response
    {
        // 1. Prioritize the route ID /{catid}, fallback to the query string ?catid=
        $targetId = $catid ?? $request->query->get('catid');

        // 2. Fetch the actual Category entity if an ID exists
        $category = $targetId ? $this->catService->find((int)$targetId) : null;

        // --- ADDED: Fetch all categories ordered by name ASC ---
        $categories = $this->catService->findAllCustom([], 'name', 'asc', ['id','name']);

        // 3. Render the view with both the object and the raw ID for JS
        return $this->render('super_admin/subcat/index.html.twig', [
            'selectedCategory' => $category,
            'catid' => $targetId,
            'categories' => $categories // New addition
        ]);
    }



    

    public function indexAjax(Request $request, ?int $catid = null): JsonResponse
    {
        // 1. DataTables standard parameters
        $draw = $request->request->getInt('draw');
        $start = $request->request->getInt('start');
        $limit = $request->request->getInt('length', 10);
        
        // 2. THE SOVEREIGN OVERWRITE: Priority POST > Query > Route Param
        $catId = $request->request->get('catid') 
                ?? $request->query->get('catid') 
                ?? $catid;

        $filters = $catId ? ['cat' => (int)$catId] : [];

        // 3. Search Logic
        $searchData = $request->request->all('search');
        if ($search = $searchData['value'] ?? null) {
            $filters['name'] = ['value' => $search, 'operator' => 'LIKE'];
        }


        $orderBy = 'id'; 
        $orderDir = 'DESC'; 
        $orderData = $request->request->all('order');

        if (!empty($orderData)) {
            $columnsData = $request->request->all('columns');
            $columnIndex = (int)$orderData[0]['column'];
            $sentOrderBy = $columnsData[$columnIndex]['data'] ?? 'id';
            $orderDir = $orderData[0]['dir'] ?? 'DESC';

            // FIX: Map the DataTables 'cat_name' to the actual Database 'cat.name'
            if ($sentOrderBy === 'cat_name') {
                $orderBy = 'cat.name'; 
            } else {
                $orderBy = $sentOrderBy;
            }
        }

        $page = ($start / $limit) + 1;
        
        // 5. Fetch data with eager loading of 'cat'
        $subcats = $this->subcatService->findPaginated($page, $limit, $filters, $orderBy, $orderDir, ['*'], ['cat']);
        
        // Scoped counts for pagination accuracy
        $totalRecords = $this->subcatService->countAll($catId ? ['cat' => (int)$catId] : []);
        $filteredRecords = $this->subcatService->countAll($filters);

        $data = [];
        foreach ($subcats as $sub) {
            $editUrl = $this->generateUrl('app_superadmin_subcat_edit', ['id' => $sub->getId()]);
            $data[] = [
                'id' => $sub->getId(),
                'cat_name' => $sub->getCat() ? $sub->getCat()->getName() : 'N/A',
                'name' => $sub->getName(),
                'image' => $sub->getImg2() ? '<img src="/'. $sub->getImg2() .'" width="40" class="rounded shadow-sm">' : '<span class="text-muted small">No Img</span>',
                'actions' => '
                <div class="btn-group">
                    <a href="' . $editUrl . '" class="btn btn-sm btn-outline-primary" title="Edit">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDelete('.$sub->getId().')" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>'
            ];
        }

        return new JsonResponse([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data,
        ]);
    }

    /**
     * THE SELECTOR: For dependent dropdowns
     * Path < Request < Empty
     */
    public function selector(Request $request, ?int $catId = null): JsonResponse
    {
        // Request overwrites Route parameter
        $finalCatId = $request->get('catid', $catId);

        if (!$finalCatId) {
            return new JsonResponse([]);
        }

        $subcats = $this->subcatService->findByCat((int)$finalCatId, [], 'name', 'ASC');

        $results = [];
        foreach ($subcats as $sub) {
            $results[] = ['id' => $sub->getId(), 'name' => $sub->getName()];
        }

        return new JsonResponse($results);
    }

// src/Controller/SuperAdmin/SubcatController.php

    public function create(Request $request, ?int $catid = null): Response
    {
        $subcat = new Subcat();

        // 1. Capture the ID: Priority Query String (?catid=) > Route Param (/{catid})
        // This specifically handles your URL: /superadmin/subcat/new?catid=19
        $targetId = $request->query->get('catid') ?? $catid;

        if ($targetId) {
            // 2. Fetch the actual Cat entity from your service
            $category = $this->catService->find((int)$targetId);
            
            if ($category) {
                // 3. THIS IS THE KEY: Pre-bind the category to the Subcat object.
                // When the form renders, the dropdown will see this and add 'selected'
                $subcat->setCat($category);
            }
        }

        // 4. Create the form with the pre-populated object
        $form = $this->createForm(SubcatType::class, $subcat);
        
        return $this->render('super_admin/subcat/create.html.twig', [
            'form' => $form->createView(),
            'catid' => $targetId // Optional: helps in Twig/JS if needed
        ]);
    }



    public function edit(int $id): Response
    {
        $subcat = $this->subcatService->find($id, ['cat']);
        if (!$subcat) throw $this->createNotFoundException('Not found.');

        $form = $this->createForm(SubcatType::class, $subcat);
        return $this->render('super_admin/subcat/edit.html.twig', [
            'subcat' => $subcat,
            'form' => $form->createView(),
        ]);
    }

    public function store(Request $request): Response
    {
        $subcat = new Subcat();
        $form = $this->createForm(SubcatType::class, $subcat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->handleFileUploads($form, $subcat);
                $this->subcatService->save($subcat);
                $this->addFlash('success', 'Subcategory saved.');
                return $this->redirectToRoute('app_superadmin_subcat_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('super_admin/subcat/create.html.twig', ['form' => $form->createView()]);
    }

    public function update(int $id, Request $request): Response
    {
        $subcat = $this->subcatService->find($id);
        if (!$subcat) throw $this->createNotFoundException('Not found.');

        $form = $this->createForm(SubcatType::class, $subcat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->handleFileUploads($form, $subcat);
                $this->subcatService->save($subcat);
                $this->addFlash('success', 'Updated successfully.');
                return $this->redirectToRoute('app_superadmin_subcat_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());
            }
        }

        return $this->render('super_admin/subcat/edit.html.twig', [
            'subcat' => $subcat,
            'form' => $form->createView(),
        ]);
    }


    private function handleFileUploads($form, Subcat $subcat): void
    {
        // 1. Get the subcategory name and sanitize (spaces/slashes to underscores)
        $rawName = $subcat->getName() ?: 'subcategory';
        $namePrefix = str_replace([' ', '/'], '_', trim($rawName));
        
        // 2. Generate the shared 8-digit random seed
        $random8 = str_pad((string)random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

        // --- IMAGE UPLOAD ---
        $imgFile = $form->get('img_file')->getData();
        if ($imgFile) {
            $upload = $this->imageService->upload(
                $imgFile, 
                'uploads/subcats/img', 
                'uploads/subcats/img/thumb', 
                1500, 1000, 
                $namePrefix, // Use sanitized subcat name as prefix
                200, 180
            );
            if ($upload) {
                $subcat->setImg($upload['large']);
                $subcat->setImg2($upload['small']);
            }
        }

        // --- FILE UPLOAD ---
        $docFile = $form->get('filer_file')->getData();
        if ($docFile) {
            // We pass the namePrefix and the random8 seed
            // Resulting filename: "SubcatName_12345678.pdf"
            $fileData = $this->fileService->upload(
                $docFile, 
                'uploads/subcats/files', 
                $namePrefix, 
                $random8
            );
            if ($fileData) {
                $subcat->setFiler($fileData['path']);
            }
        }
    }

    public function delete(Request $request, int $id): Response
    {
        if (!$this->isCsrfTokenValid('app_superadmin', $request->request->get('_token'))) {
            return $this->redirectToRoute('app_superadmin_subcat_index');
        }

        $subcat = $this->subcatService->find($id);
        if ($subcat) $this->subcatService->delete($subcat);

        return $this->redirectToRoute('app_superadmin_subcat_index');
    }
    
    public function deleteMany(Request $request): Response
    {
        // 1. Verify CSRF Token (Matching the one in your form)
        if (!$this->isCsrfTokenValid('app_superadmin', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Security Breach: Invalid Bulk Token.');
            return $this->redirectToRoute('app_superadmin_subcat_index');
        }

        // 2. Get IDs from POST
        $ids = $request->request->all('ids');

        if (empty($ids)) {
            $this->addFlash('warning', 'No subcategories selected.');
        } else {
            try {
                // 3. Use the SubcatService to purge the entries
                $this->subcatService->deleteBulk($ids);
                $this->addFlash('success', count($ids) . ' subcategory entries purged.');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Purge Failed: ' . $e->getMessage());
            }
        }

        // 4. Redirect back to Subcat index
        return $this->redirectToRoute('app_superadmin_subcat_index');
    }
public function checkName(Request $request): JsonResponse
    {
        // 1. Extract name and parent category from POST
        $name = $request->request->get('name');
        $catId = $request->request->get('catid'); 

        // 2. THE FIX: Capture 'ignoreId' from POST data, not the route
        $ignoreId = $request->request->get('ignoreId');

        // If we don't have name or parent cat, we can't perform the scoped check
        if (!$name || !$catId) {
            return new JsonResponse([
                'exists' => false, 
                'status' => 'neutral',
                'message' => 'Waiting for category and name...'
            ]);
        }

        // 3. Pass the $ignoreId to the service. 
        // If it's a new subcat, $ignoreId will be null/empty. 
        // If it's an edit, it will be the ID we want the DB to skip.
        $exists = $this->subcatService->nameExistsInSubcat(
            $name, 
            (int)$catId, 
            $ignoreId ? (int)$ignoreId : null
        );

        return new JsonResponse([
            'exists' => $exists,
            'status' => $exists ? 'conflict' : 'available',
            'message' => $exists 
                ? "This name is already used in this category." 
                : "Name available in this registry."
        ]);
    }

}
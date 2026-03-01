<?php

namespace App\Controller\SuperAdmin;

use App\Entity\Cat;
use App\Form\CatType;
use App\Service\CatService;
use App\Service\ImageUploadService;
use App\Service\FileUploaderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\HttpFoundation\Response;

class CatController extends AbstractController
{
    public function __construct(
        private CatService $catService,
        private ImageUploadService $imageService,
        private FileUploaderService $fileService
    ) {}

    /**
     * Just the view. DataTables fetches data via indexAjax.
     */
    public function index(): Response
    {
        return $this->render('super_admin/cat/index.html.twig');
    }

    /**
     * The AJAX Engine for DataTables
     */
    public function indexAjax(Request $request): JsonResponse
    {
        $draw = $request->request->getInt('draw');
        $start = $request->request->getInt('start');
        $limit = $request->request->getInt('length', 5);
        
        $searchData = $request->request->all('search');
        $search = $searchData['value'] ?? null;

        $orderData = $request->request->all('order');
        $columnsData = $request->request->all('columns');
        
        $orderBy = 'id'; 
        $orderDir = 'DESC'; 

        if (!empty($orderData)) {
            $columnIndex = $orderData[0]['column'];
            $orderBy = $columnsData[$columnIndex]['data'];
            $orderDir = $orderData[0]['dir'];
        }

        $page = ($start / $limit) + 1;
        $filters = $search ? ['name' => ['value' => $search, 'operator' => 'LIKE']] : [];
        
        $cats = $this->catService->findPaginated($page, $limit, $filters, $orderBy, $orderDir);        
        $totalRecords = $this->catService->countAll();
        $filteredRecords = $this->catService->countAll($filters);

        $data = [];
        foreach ($cats as $cat) {
            // New Feature: Get the count of children
            $subCount = count($cat->getSubcats()); 
            $countLabel = ($subCount > 0) ? ' ('.$subCount.')' : '';

            $data[] = [
                'id' => $cat->getId(),
                'name' => $cat->getName(),
                'image' => $cat->getImg2() ? '<img src="/'. $cat->getImg2() .'" width="40" class="rounded">' : 'No Img',
                'file' => $cat->getFiler() ? '<span class="badge bg-info">Attached</span>' : 'None',
                'actions' => '
                    <div class="btn-group">
                        <a href="'.$this->generateUrl('app_superadmin_subcat_index', ['catid' => $cat->getId()]).'" 
                           class="btn btn-sm btn-outline-success" 
                           title="View Subcategories">
                           View Subcategories' . $countLabel . '
                        </a>
                        <a href="'.$this->generateUrl('app_superadmin_subcat_create', ['catid' => $cat->getId()]).'" 
                           class="btn btn-sm btn-outline-success" 
                           title="Add Subcategory">
                           + Subcategory
                        </a>
                        <a href="'.$this->generateUrl('app_superadmin_cat_edit', ['id' => $cat->getId()]).'" 
                           class="btn btn-sm btn-outline-secondary">Edit</a>
                        <button type="button" 
                                class="btn btn-sm btn-outline-danger" 
                                onclick="confirmDelete('.$cat->getId().')">Delete</button>
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

    public function create(): Response
    {
        $form = $this->createForm(CatType::class, new Cat());
        return $this->render('super_admin/cat/create.html.twig', ['form' => $form->createView()]);
    }



    public function edit(int $id): Response
    {
        // Using the new Sovereign find with optional eager loading
        $cat = $this->catService->find($id); // Add ['subcats'] here if needed
        if (!$cat) throw $this->createNotFoundException('Cat not found.');

        $form = $this->createForm(CatType::class, $cat);
        return $this->render('super_admin/cat/edit.html.twig', [
            'cat' => $cat,
            'form' => $form->createView(),
        ]);
    }


    public function store(Request $request): Response
    {
        $cat = new Cat();
        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->handleFileUploads($form, $cat);
                    $this->catService->save($cat);
                    $this->addFlash('success', 'Category added.');
                    return $this->redirectToRoute('app_superadmin_cat_index');
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Error: ' . $e->getMessage());
                }
            } else {
                $this->addFlash('danger', 'Validation Failed.');
            }
        }

        return $this->render('super_admin/cat/create.html.twig', ['form' => $form->createView()]);
    }

    public function update(int $id, Request $request): Response
    {
        $cat = $this->catService->find($id);
        if (!$cat) throw $this->createNotFoundException('Entry does not exist.');

        $form = $this->createForm(CatType::class, $cat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->handleFileUploads($form, $cat);
                $this->catService->save($cat);
                $this->addFlash('success', 'Updated successfully.');
                return $this->redirectToRoute('app_superadmin_cat_index');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Update Failure: ' . $e->getMessage());
            }
        }

        return $this->render('super_admin/cat/edit.html.twig', [
            'cat' => $cat,
            'form' => $form->createView(),
        ]);
    }

    private function handleFileUploads($form, Cat $cat): void
    {
        // 1. Specify the name (prefix) from the entity
        $namePrefix = $cat->getName() ?: 'category';
        
        // 2. Generate the 8-digit random string
        $random8 = str_pad((string)random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

        // Handle Image: Pass $namePrefix as the 6th argument
        $imgFile = $form->get('img_file')->getData();
        if ($imgFile) {
            $upload = $this->imageService->upload(
                $imgFile, 
                'uploads/cats/img', 
                'uploads/cats/img/thumb', 
                1500, 
                1000, 
                $namePrefix, // This specifies the name
                200, 
                180
            );
            if ($upload) {
                $cat->setImg($upload['large']);
                $cat->setImg2($upload['small']);
            }
        }

        // Handle Document: Pass $namePrefix and $random8
        $docFile = $form->get('filer_file')->getData();
        if ($docFile) {
            $fileData = $this->fileService->upload(
                $docFile, 
                'uploads/cats/files', 
                $namePrefix, // This specifies the name
                $random8     // This specifies the 8 digits
            );
            if ($fileData) {
                $cat->setFiler($fileData['path']);
            }
        }
    }

    public function delete(Request $request, int $id): Response
    {
        if (!$this->isCsrfTokenValid('app_superadmin', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Security Breach: Invalid Token.');
            return $this->redirectToRoute('app_superadmin_cat_index');
        }

        $cat = $this->catService->find($id);
        if ($cat) {
            $this->catService->delete($cat);
            $this->addFlash('success', "Entry #$id removed.");
        }

        return $this->redirectToRoute('app_superadmin_cat_index');
    }

    public function deleteMany(Request $request): Response
    {
        if (!$this->isCsrfTokenValid('app_superadmin', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Security Breach: Invalid Bulk Token.');
            return $this->redirectToRoute('app_superadmin_cat_index');
        }

        $ids = $request->request->all('ids');
        if (empty($ids)) {
            $this->addFlash('warning', 'No entries selected.');
        } else {
            try {
                $this->catService->deleteBulk($ids);
                $this->addFlash('success', count($ids) . ' entries purged.');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Purge Failed: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_superadmin_cat_index');
    }

    public function checkName(Request $request, ?int $id = null): JsonResponse
    {
        $name = $request->request->get('name'); 
        if (!$name || strlen(trim($name)) <= 2) {
            return new JsonResponse(['status' => 'neutral', 'exists' => false]);
        }

        $ignoreId = $id ?? $request->request->get('ignoreId');
        $exists = $this->catService->nameExists($name, $ignoreId ? (int)$ignoreId : null);

        return new JsonResponse([
            'exists' => $exists,
            'status' => $exists ? 'conflict' : 'available',
            'message' => $exists ? "Name '$name' taken." : "Name '$name' available."
        ]);
    }


}
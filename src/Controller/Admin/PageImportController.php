<?php

namespace App\Controller\Admin;

use App\Form\ProductImportType;
use App\Form\UpdatePricesType;
use App\Model\Admin\ProductImport;
use App\Model\Admin\UpdatePrices;
use App\Service\ImportManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class PageImportController extends AbstractController
{
    /**
     * @Route("/admin/product-import", name="admin_product_import")
     */
    public function products(Request $request, ImportManager $manager): Response
    {
        $productImport = new ProductImport();
        $form          = $this->createForm(ProductImportType::class, $productImport);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try{
                [$created, $updated] = $manager->importProducts($productImport);
            } catch (Throwable $e){
                $this->addFlash('warning', $e->getMessage());
            }
        }
        
        return $this->render('admin/page-import/index.html.twig', [
            'form'    => $form->createView(),
            'created' => $created ?? [],
            'updated' => $updated ?? [],
        ]);
    }
    
    /**
     * @Route("/admin/update-prices", name="admin_update_prices")
     */
    public function updatePrices(Request $request, ImportManager $manager): Response
    {
        $updatePricesDto = new UpdatePrices();
        $form            = $this->createForm(UpdatePricesType::class, $updatePricesDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try{
                $updated = $manager->updatePrices($updatePricesDto);
            } catch (Throwable $e){
                $this->addFlash('warning', $e->getMessage());
            }
        }
        
        return $this->render('admin/update-prices/index.html.twig', [
            'form'     => $form->createView(),
            'updated'  => $updated ?? [],
        ]);
    }

}
<?php

namespace App\Service;

use App\Entity\Color;
use App\Entity\Content;
use App\Entity\Measure;
use App\Entity\Products;
use App\Model\Admin\ProductImport;
use App\Model\Admin\UpdatePrices;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

class ImportManager
{
    private EntityManagerInterface $entityManager;
    private Xlsx $xlsxReader;
    private string $projectDir;

    public function __construct(
        EntityManagerInterface $entityManager,
        Xlsx                   $xlsxReader,
        string                 $projectDir
    )
    {
        $this->entityManager = $entityManager;
        $this->xlsxReader = $xlsxReader;
        $this->projectDir = $projectDir;
    }

    public function importProducts(ProductImport $productImport): array
    {
        $sheetData = $this->getSheetData($productImport->getXlsFile());
        $created = [];
        $updated = [];
        $productRepo = $this->entityManager->getRepository(Products::class);
        foreach ($sheetData as $row_number => $row) {
            if ($row_number < $productImport->getFirstRow()) {
                continue;
            }
            [
                "A" => $productName,
                "B" => $slug,
                "C" => $material,
                "D" => $imageName,
                "E" => $price,
                "F" => $colorName,
            ] = $row;

            if (empty($productName)) {
                continue;
            }

            /** @var Products|null $product */
            $product = $productRepo->findOneBy([
                'name' => $productName,
                'category_id' => $productImport->getCategory()
            ]);

            if (null === $product) {
                $product = $this->initProduct($productName, $slug, $productImport);
                $created[] = $product->getPathWithSlash();
            } else {
                $updated[] = $product->getPathWithSlash();
            }
            $this->setColor($colorName, $product);
            $this->setImages($imageName, $product);
            $this->setPrice($price, $product);
            $this->setMeasure($product);
            $this->setCardText($product, $material, $colorName);

        }
        $this->entityManager->flush();

        $imgFolder = $this->projectDir . '/public/uploads/products/';

        $this->importImages(
            $productImport->getImagesBig(),
            $imgFolder . 'big/'
        );

        $this->importImages(
            $productImport->getImagesCatalog(),
            $imgFolder
        );

        return [$created, $updated];
    }

    public function updatePrices(UpdatePrices $updatePrices): array
    {
        $factor = 1 + ($updatePrices->getPercent() / 100);

        $allCategories = $updatePrices
            ->getContent()
            ?->getChildrenCategoryIdsRecursive($this->entityManager->getRepository(Content::class));

        $products = $this->entityManager
            ->getRepository(Products::class)
            ->findBy(['category_id' => $allCategories]);

        foreach ($products as $product) {
            $product->setOldPrice($product->getPrice());
            $newPrice = round($product->getPrice() * $factor);
            $product->setPrice((int)$newPrice);
        }
        
        $this->entityManager->flush();
        
        return $products;
    }

    private function importImages(?UploadedFile $zippedImages, string $folder): void
    {
        if (null === $zippedImages) {
            return;
        }

        $zip = new ZipArchive();
        $zip->open($zippedImages->getRealPath());
        $zip->extractTo($folder);
        $zip->close();
    }


    private function setColor(string $colorName, Products $product): void
    {
        if (!empty($colorName)) {
            $colorRepo = $this->entityManager->getRepository(Color::class);
            $color = $colorRepo->findOneBy(['name' => $colorName]);
            if (null === $color) {
                throw new RuntimeException(sprintf('Цвет "%s" не найден', $colorName));
            }
            $product->setColor($color);
        }
    }


    private function initProduct(string $productName, string $slug, ProductImport $productImport): Products
    {
        $product = (new Products())
            ->setName($productName)
            ->setCategoryId($productImport->getCategory())
            ->setPath(sprintf('%s/%s/', $productImport->getBaseUri(), $slug));
        $this->entityManager->persist($product);

        return $product;
    }


    private function setImages(string $imageName, Products $product): void
    {
        if (!empty($imageName)) {
            $product
                ->setImage($imageName)
                ->setBigImg($imageName);
        }
    }

    private function setPrice(string $price, Products $product): void
    {
        if (!empty($price)) {
            $product->setPrice(preg_replace('/\D/','',$price));
        }
    }

    private function getSheetData(UploadedFile $xlsFile): array
    {
        $spreadsheet = $this->xlsxReader->load($xlsFile);

        return $spreadsheet
            ->getActiveSheet()
            ->toArray(null, true, true, true);
    }

    private function setMeasure(Products $product):void
    {
        $measure = $this->entityManager->getRepository(Measure::class)->find(2);
        $product->setMeasure($measure);
    }

    private function setCardText(Products $product, string $material, string $colorName):void
    {
        $product->setCardText("<div><strong>Материал столешницы:</strong> $material</div>
<div><strong>Цвет:</strong> $colorName</div>
<div><strong>Стиль:</strong> на ваш выбор</div>
<div><strong>Конструкция:</strong> по вашему желанию (12 профилей на выбор).</div>
<div><strong>Размер:</strong> подбирается индивидуально.</div>
<div><strong>Выезд и замер:</strong> бесплатный при оформлении заказа и 100% оплате.</div>
<div><strong>Доставка и установка столешниц:</strong> Осуществляется по Москве и области.</div>");
    }
}
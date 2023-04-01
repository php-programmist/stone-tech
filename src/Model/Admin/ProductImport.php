<?php

namespace App\Model\Admin;


use App\Entity\Category;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductImport
{
    private ?UploadedFile $xlsFile = null;
    
    private int $firstRow = 2;
    
    private ?Category $category = null;
    
    private ?UploadedFile $imagesBig = null;
    
    private ?UploadedFile $imagesCatalog = null;
    
    private ?string $baseUri = null;
    
    /**
     * @return UploadedFile|null
     */
    public function getXlsFile(): ?UploadedFile
    {
        return $this->xlsFile;
    }
    
    /**
     * @param UploadedFile|null $xlsFile
     *
     * @return $this
     */
    public function setXlsFile(?UploadedFile $xlsFile): self
    {
        $this->xlsFile = $xlsFile;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getFirstRow(): int
    {
        return $this->firstRow;
    }
    
    /**
     * @param int $firstRow
     *
     * @return $this
     */
    public function setFirstRow(int $firstRow): self
    {
        $this->firstRow = $firstRow;
        
        return $this;
    }
    
    /**
     * @return Category|null
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }
    
    /**
     * @param Category|null $category
     *
     * @return $this
     */
    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        
        return $this;
    }

    
    /**
     * @return UploadedFile|null
     */
    public function getImagesBig(): ?UploadedFile
    {
        return $this->imagesBig;
    }
    
    /**
     * @param UploadedFile|null $imagesBig
     *
     * @return $this
     */
    public function setImagesBig(?UploadedFile $imagesBig): self
    {
        $this->imagesBig = $imagesBig;
        
        return $this;
    }
    
    /**
     * @return UploadedFile|null
     */
    public function getImagesCatalog(): ?UploadedFile
    {
        return $this->imagesCatalog;
    }
    
    /**
     * @param UploadedFile|null $imagesCatalog
     *
     * @return $this
     */
    public function setImagesCatalog(?UploadedFile $imagesCatalog): self
    {
        $this->imagesCatalog = $imagesCatalog;
        
        return $this;
    }
    
    /**
     * @return string|null
     */
    public function getBaseUri(): ?string
    {
        return trim($this->baseUri, ' /');
    }
    
    /**
     * @param string|null $baseUri
     *
     * @return $this
     */
    public function setBaseUri(?string $baseUri): self
    {
        $this->baseUri = $baseUri;
        
        return $this;
    }
    
}
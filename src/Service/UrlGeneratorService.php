<?php


namespace App\Service;

use App\Entity\Products;
use App\Entity\StoneCatalog;
use App\Entity\StoneProduct;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;

class UrlGeneratorService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var ContentRepository
     */
    protected $contentRepository;

    public function __construct(
        EntityManagerInterface $em,
        ContentRepository $contentRepository
    ){
       $this->em = $em;
       $this->contentRepository = $contentRepository;
        $connection = $this->em->getConnection();
    }

    public function generateUrlByNewStoneCategory(StoneCatalog $stoneCatalog){
        $catalogItem = $stoneCatalog->setSlug('katalog/'.$stoneCatalog->getSlug().'/');
        $this->em->persist($catalogItem);
        $this->em->flush();
    }

    public function generateUrlByNewStoneProduct(StoneProduct $stoneProduct){
        $itemPath = $stoneProduct->setSlug($stoneProduct->getParent()->getSlug().$stoneProduct->getSlug().'/');
        $this->em->persist($itemPath);
        $this->em->flush();

    }

    public function generateUrlByProduct(Products $products){

        $itemPath = $products->setPath(rtrim($products->getPath(),'/').'/');
        $this->em->persist($itemPath);
        $this->em->flush();
    }


}
<?php

namespace App\Controller;

use App\Entity\Content;
use App\Entity\Products;
use App\Entity\StoneCatalog;
use App\Entity\StoneProduct;
use App\Entity\CityPages;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ContentRepository;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use App\Repository\ProductsRepository;
use Knp\Component\Pager\Paginator;
use App\Repository\ColorRepository;
use App\Repository\StoneCatalogRepository;
use App\Repository\StoneProductRepository;
use App\Repository\CityPagesRepository;



class PageController extends AbstractController
{
    /**
     * @var ContentRepository
     */
   protected $page_repository;
    /**
     * @var PaginatorInterface
     */
    protected $paginator;
    /**
     * @var ProductsRepository
     */
   protected $products_repository;
    /**
     * @var ColorRepository
     */
   protected $color_repository;

    /**
     * @var StoneCatalogRepository
     */
    protected $stoneCatalogRepository;

    /**
     * @var StoneProductRepository
     */
    protected $stoneProductRepository;

    /**
     * @var CityPagesRepository
     */
    protected $cityPagesRepository;
    /**
     * @var int[]
     */
    private $hide_price_array;
    /**
     * @var int[]
     */
    private $districtsCategory;


    public function __construct(ContentRepository $repository, ProductsRepository $productsRepository, PaginatorInterface $paginator, ColorRepository $color_repository, StoneCatalogRepository $stoneCatalogRepository, StoneProductRepository $stoneProductRepository, CityPagesRepository $cityPagesRepository)
   {
       $this->page_repository = $repository;
       $this->products_repository = $productsRepository;
       $this->paginator = $paginator;
       $this->color_repository = $color_repository;
       $this->stoneCatalogRepository = $stoneCatalogRepository;
       $this->stoneProductRepository = $stoneProductRepository;
       $this->cityPagesRepository = $cityPagesRepository;
       $this->hide_price_array =  array(1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 21, 22, 23);
       $this->districtsCategory = array(1, 2, 3, 6, 7, 8, 11, 12, 13, 21, 22, 23);
   }

    /**
     * @Route ("/{token}districts/", name="districts", requirements={"token"=".+\/$"})
     */
    public function districts($token, Request $request):Response{
        if(!$page =  $this->page_repository->findOneBy(['path'=>$token]) or in_array($page->getCategoryId()->getId(), $this->districtsCategory) == false){
            throw $this->createNotFoundException(sprintf('Page %s not found', $token.'districts'));
        }
        $cities = $this->cityPagesRepository->findBy(['parent' => $page->getId()], ['name' => 'ASC']);
        return $this->render('districts/index.html.twig', [
            'page' => $page,
            'cityPage' => true,
            'cities' => $cities,
        ]);
    }

    /**
     * @Route("/{token}color/{color_token}", name="dynamic_catalog", requirements={"token"= ".+\/$", "color_token"=".+"})
     */
    public function catalog_color($token, $color_token,  PaginatorInterface $paginator, Request  $request, EntityManagerInterface $em):Response{
        if(!$page = $this->products_repository->findOneBy(['path'=>$token]) AND !$page = $this->page_repository->findOneBy(['path'=>$token]) ){
            throw $this->createNotFoundException(sprintf('Page %s not found', $token));
        }
        if(!$color = $this->color_repository->findOneBy(['slug' => $color_token]) ){
            throw $this->createNotFoundException(sprintf('Color %s not found', $color_token));
        }
        if($page instanceof Content) {
            if ($page->getPageType() == 'category') {
                return $this->category_color($page, $color->getId(), $paginator, $request);
            }
        }

    }

    /**
     * @Route("/{token}", name="dynamic_pages",requirements={"token"= ".+\/$"})
     */

    public function index($token, PaginatorInterface $paginator, Request $request, EntityManagerInterface $em){
        if(!$page = $this->products_repository->findOneBy(['path'=>$token]) AND !$page = $this->page_repository->findOneBy(['path'=>$token]) AND !$page = $this->stoneCatalogRepository->findOneBy(['slug'=>$token]) AND !$page = $this->stoneProductRepository->findOneBy(['slug'=>$token]) AND !$page = $this->cityPagesRepository->findOneBy(['path'=>$token])){
            throw $this->createNotFoundException(sprintf('Page %s not found', $token));
        }

        if($page instanceof Products){
            return $this->product($page);
        }
        if($page instanceof StoneCatalog){
            $items = $this->stoneProductRepository->findBy(['parent' => $page->getId()]);
            return $this->render('stone_catalog/list.html.twig',
                [
                    'page' => $page,
                    'items' => $items,
                    ]
            );
        }

        if($page instanceof StoneProduct){
            $product = $this->stoneProductRepository->findOneBy(['slug' => $token]);
            return $this->render('stone_catalog/item.html.twig',
                [
                    'page' => $page,
                    'product' => $product,
                ]
            );
        }

        if($page instanceof Content) {
            if ($page->getPageType() === 'category') {
                return $this->category($page, $paginator, $request);
            }
            //потом над этим подумать
            if ($page->getPageType() === 'simple') {
                return $this->simple($page);
            }

            //каталог камня
            if($page->getPageType() === 'stonecatalog'){
                return $this->stoneCatalog($page);

            }
        }

        if($page instanceof CityPages){
            return $this->cityPage($page);
        }

    }

    private function cityPage($page){
        $stairs_steps = false;
        if(in_array($page->getParent()->getId(), array(3,14,15,7,18,19))){
            $stairs_steps = true;
        }
        return $this->render('city_page/index.html.twig',
        [
            'page' => $page,
            'cityPage' => true,
            'stairsSteps' => $stairs_steps,
        ]);
    }

    private function stoneCatalog($page){
        $categories = $this->stoneCatalogRepository->findAll();
        foreach ($categories as $category){
            $images_thumb = $this->stoneProductRepository->findBy(['parent' => $category->getId()],[],4);
            $category->thumbs = $images_thumb;
        }
        return $this->render('stone_catalog/index.html.twig', [
                    'page' => $page,
                    'categories' => $categories,
                ]);
    }

    private function simple($simple){
        return $this->render('page/simple.html.twig',[
           'page' => $simple
        ]);
    }

    private function category(Content $category, $paginator, $request){
        //определяем страницу, на которой находимся

        $page = 1;
        $startPage = 1;
        if(isset($_POST['ajax']) && isset($_POST['page'])){
            $page = $_POST['page'];
            $startPage = $_POST['startPage'];
        }

        $sort = null;
        if (isset($_POST['ajax']) && isset($_POST['sort'])){
            $sort = $_POST['sort'];
        }

        $ourWorks = $this->getOurWorkImages($category->getPath());

        $category_children = $this->page_repository->findBy(['parent' => $category->getId()]);


        if(empty($category_children)){
            if(isset($_POST['ajax']) && isset($_POST['page'])){
                $products = $this->getProductsMore($this->products_repository, $category->getCategoryId(), $sort, $page, $startPage);
                $all_products = $this->products_repository->findBy(['category_id' => $category->getCategoryId()]);
                $limit = 20;
                $pagination = $paginator->paginate(
                    $all_products, /* query NOT result */
                    $request->query->getInt('page', $page+1), /*page number*/
                    $limit /*limit per page*/
                );
            }
            else {
                $products = $this->getProducts($this->products_repository, $category->getCategoryId(), $sort);
                $pagination = $paginator->paginate(
                    $products,
                    $request->query->getInt('page', 1),
                    20
                );
            }
            $min_price = $this->products_repository->findOneBy(['category_id' =>$category->getCategoryId()], ['price'=>'ASC']);
        }
        else{
            $category_arr = [$category->getCategoryId()];
            foreach ($category_children as $item){
                $category_arr[] = $item->getCategoryId();
            }
            if(isset($_POST['ajax']) && isset($_POST['page'])){
                $products = $this->getProductsFromChildrenMore($this->products_repository, $category_arr, $sort, $page, $startPage);
                $all_products = $this->products_repository->findAll();
                $limit = 20;
                $pagination = $paginator->paginate(
                    $all_products, /* query NOT result */
                    $request->query->getInt('page', $page+1), /*page number*/
                    $limit /*limit per page*/
                );
            }else{
                $products = $this->getProductsFromChildren($this->products_repository, $category_arr, $sort);
                $pagination = $paginator->paginate(
                    $products,
                    $request->query->getInt('page', 1),
                    20
                );
            }
            $min_price = $this->products_repository->findOneBy(['category_id' =>$category_arr], ['price'=>'ASC']);
        }

        $pagination->setParam('_fragment', 'catalog-anchor');
        $colors = $this->getColors($products);


        if($category->getId() == 7){
            $citiesBlock = $this->cityPagesRepository->findBy(['parent' => 3, 'show_in_block' => 1], ['name' => 'ASC']);
        }elseif ($category->getId() == 18){
            $citiesBlock = $this->cityPagesRepository->findBy(['parent' => 14, 'show_in_block' => 1], ['name' => 'ASC']);
        }elseif ($category->getId() == 19){
            $citiesBlock = $this->cityPagesRepository->findBy(['parent' => 15, 'show_in_block' => 1], ['name' => 'ASC']);
        }else {
            $citiesBlock = $this->cityPagesRepository->findBy(['parent' => $category->getId(), 'show_in_block' => 1], ['name' => 'ASC']);
        }


        if (isset($_POST['ajax']) && isset($_POST['page'])) {

                return $this->render('ajax/catalog_more.html.twig', [
                    'path' => $category->getPath(),
                    'category' => $category->getCategoryId(),
                    'products' => $products,
                    'colors' => $colors,
                    'pagination' => $pagination,
                    'activeColor' => null,
                    'hidePriceArray' => in_array($category->getCategoryId(), $this->hide_price_array),
                    'citiesBlock' => $citiesBlock,
                ]);

        }

        if(isset($_POST['ajax'])) {
            return $this->render('ajax/catalog.html.twig', [
                'path' => $category->getPath(),
                'category' => $category->getCategoryId(),
                'products' => $products,
                'colors' => $colors,
                'pagination' => $pagination,
                'activeColor' => null,
                'hidePriceArray' => in_array($category->getCategoryId()->getId(), $this->hide_price_array),
                'citiesBlock' => $citiesBlock,
            ]);
        }

        return $this->render($category->getTemplate() ?? 'page/category.html.twig',[
           'category'=>$category,
            'works' => $ourWorks,
            'products' => $products,
            'colors' => $colors,
            'pagination'=>$pagination,
            'activeColor' => null,
            'minPrice' => $min_price,
            'categoryChildren' => $category_children,
            'hidePriceArray' => in_array($category->getCategoryId()->getId(), $this->hide_price_array),
            'citiesBlock' => $citiesBlock,
        ]);
    }

    private function category_color($category, $color, $paginator, $request){
        //определяем страницу, на которой находимся

        $page = 1;
        $startPage = 1;
        if(isset($_POST['ajax']) && isset($_POST['page'])){
            $page = $_POST['page'];
            $startPage = $_POST['startPage'];
        }

        $sort = null;
        if (isset($_POST['ajax']) && isset($_POST['sort'])){
            $sort = $_POST['sort'];
        }

        $ourWorks = $this->getOurWorkImages($category->getPath());

        $category_children = $this->page_repository->findBy(['parent' => $category->getId()]);
        //если не общая стр
        if(empty($category_children)) {
            if(isset($_POST['ajax']) && isset($_POST['page'])){
                $products = $this->getProductsByColorMore($this->products_repository, $this->color_repository, $category->getCategoryId(), $color, $sort, $page, $startPage);
                $limit = 20;
                $all_products = $this->products_repository->findBy(['category_id' => $category->getCategoryId(), 'color' => $color]);
                $all_category_products = $this->getProducts($this->products_repository, $category->getCategoryId(), $sort);
                $pagination = $paginator->paginate(
                    $all_products, /* query NOT result */
                    $request->query->getInt('page', $page+1), /*page number*/
                    $limit /*limit per page*/
                );
            }
            else {
                $products = $this->getProductsByColor($this->products_repository, $this->color_repository, $category->getCategoryId(), $color, $sort);
                $all_category_products = $this->getProducts($this->products_repository, $category->getCategoryId(), $sort);
                $pagination = $paginator->paginate(
                    $products,
                    $request->query->getInt('page', 1),
                    20
                );
            }
        }
        //если общая
        else {
            $category_arr = array();
            foreach ($category_children as $item) {
                $category_arr[] = $item->getCategoryId();
            }
            if(isset($_POST['ajax']) && isset($_POST['page'])) {
                $products = $this->getProductsByColorFromChildMore($this->products_repository, $this->color_repository, $category_arr, $color, $sort, $page, $startPage);
                $all_products = $this->products_repository->findBy(['category_id' => $category_arr, 'color' => $color]);
                $limit = 20;
                $pagination = $paginator->paginate(
                    $all_products, /* query NOT result */
                    $request->query->getInt('page', $page+1), /*page number*/
                    $limit /*limit per page*/
                );
                $all_category_products = $this->getProducts($this->products_repository, $category_arr, $sort);
            } else {
                $products = $this->getProductsByColorFromChild($this->products_repository, $this->color_repository, $category_arr, $color, $sort);
                $pagination = $paginator->paginate(
                    $products,
                    $request->query->getInt('page', 1),
                    20
                );
                $all_category_products = $this->getProducts($this->products_repository, $category_arr, $sort);
            }
        }



        $pagination->setParam('_fragment', 'catalog-anchor');
        $colors = $this->getColors($all_category_products);
        $colorName = $this->color_repository->find($color);

        if (isset($_POST['ajax']) && isset($_POST['page'])) {
            return $this->render('ajax/catalog_more.html.twig',[
                'path'=>$category->getPath(),
                'category' =>$category->getCategoryId(),
                'products' => $products,
                'colors' => $colors,
                'pagination'=>$pagination,
                'activeColor' => null,
                'categoryChildren' => $category_children,
                'hidePriceArray' => in_array($category->getCategoryId()->getId(), $this->hide_price_array),
            ]);
        }

        if(isset($_POST['ajax'])) {
            return $this->render('ajax/catalog.html.twig',[
                'path'=>$category->getPath(),
                'category' =>$category->getCategoryId(),
                'products' => $products,
                'colors' => $colors,
                'pagination'=>$pagination,
                'activeColor' => null,
                'categoryChildren' => $category_children,
                'hidePriceArray' => in_array($category->getCategoryId()->getId(), $this->hide_price_array),
            ]);
        }

        return $this->render('page/category_color.html.twig',[
            'category'=>$category,
            'works' => $ourWorks,
            'products' => $products,
            'colors' => $colors,
            'pagination'=>$pagination,
            'activeColor' => $color,
            'colorName' => $colorName->getColorPlural(),
            'colorPath' => $colorName->getSlug(),
            'categoryChildren' => $category_children,
            'hidePriceArray' => in_array($category->getCategoryId()->getId(), $this->hide_price_array),
        ]);
    }


    private function product($product){
        return $this->render('product/index.html.twig',[
            'product'=>$product,
            'hidePriceArray' => in_array($product->getCategoryId()->getId(), $this->hide_price_array),
        ]);
    }

    private function getOurWorkImages($folderName){
        $finder = new Finder();
        $folder = trim(str_replace('/',' ', $folderName));
        $folder = str_replace(' ','-', $folder);
        $filesystem = new Filesystem();
        if($filesystem->exists($_SERVER['DOCUMENT_ROOT'].'/images/our_works/'.$folder)){
            $finder->files()->name(['*.jpeg','*.jpg','*.png'])->in($_SERVER['DOCUMENT_ROOT'].'/images/our_works/'.$folder);
            $files = array();
            foreach ($finder as $file){
                $files[] = '/images/our_works/'.$folder.'/'.$file->getFilename();
            }
        }else{
            $files = null;
        }


        return $files;
    }

    private function getProducts(ProductsRepository $productsRepository, $categoryId, $sort){
        if(!isset($sort)){
            $products = $productsRepository->findBy(['category_id' => $categoryId]);
        }else{
            $products = $productsRepository->findBy(['category_id' => $categoryId], ['price'=>$sort]);
        }
        return $products;
    }
    private function getProductsMore(ProductsRepository $productsRepository, $categoryId, $sort, $page, $startPage){
        if(!isset($sort)){
            $products = $productsRepository->findBy(['category_id' => $categoryId], ['price'=>'ASC'], 20*($page+1), 20*$startPage-20);
        }else{
            $products = $productsRepository->findBy(['category_id' => $categoryId], ['price'=>$sort], 20*($page+1), 20*$startPage-20);
        }
        return $products;
    }

    private function getProductsFromChildren(ProductsRepository $productsRepository, $categoriesArr, $sort){
        if(!isset($sort)){
            $products = $productsRepository->findBy(['category_id' => $categoriesArr], ['price'=>'ASC']);
        }else{
            $products = $productsRepository->findBy(['category_id' => $categoriesArr], ['price'=>$sort]);
        }
        return $products;
    }

    private function getProductsFromChildrenMore(ProductsRepository $productsRepository, $categoriesArr, $sort, $page, $startPage){
        if(!isset($sort)){
            $products = $productsRepository->findBy(['category_id' => $categoriesArr], ['price'=>'ASC'], 20*($page+1), 20*$startPage-20);
        }else{
            $products = $productsRepository->findBy(['category_id' => $categoriesArr], ['price'=>$sort], 20*($page+1), 20*$startPage-20);
        }
        return $products;
    }

    private function getProductsByColor(ProductsRepository $productsRepository, ColorRepository $colorRepository, $categoryId, $color, $sort){
        if(!isset($sort)){
            $products = $productsRepository->findBy([
                'category_id' => $categoryId,
                'color' => $color,
            ]);
        }else {
            $products = $productsRepository->findBy([
                'category_id' => $categoryId,
                'color' => $color,
            ], ['price' => $sort]);
        }
        return $products;
    }

    private function getProductsByColorMore(ProductsRepository $productsRepository, ColorRepository $colorRepository, $categoryId, $color, $sort, $page, $startPage){
        if(!isset($sort)){
            $products = $productsRepository->findBy([
                'category_id' => $categoryId,
                'color' => $color,
            ], [],20*($page+1), 20*$startPage-20);
        }else {
            $products = $productsRepository->findBy([
                'category_id' => $categoryId,
                'color' => $color,
            ], ['price'=>$sort], 20*($page+1), 20*$startPage-20);
        }
        return $products;
    }

    private function getProductsByColorFromChild(ProductsRepository $productsRepository, ColorRepository $colorRepository, $categoriesArr, $color, $sort){
        if(!isset($sort)){
            $products = $productsRepository->findBy([
                'category_id' => $categoriesArr,
                'color' => $color,
            ]);
        }else {
            $products = $productsRepository->findBy([
                'category_id' => $categoriesArr,
                'color' => $color,
            ], ['price' => $sort]);
        }
        return $products;
    }

    private function getProductsByColorFromChildMore(ProductsRepository $productsRepository, ColorRepository $colorRepository, $categoriesArr, $color, $sort, $page, $startPage){
        if(!isset($sort)){
            $products = $productsRepository->findBy([
                'category_id' => $categoriesArr,
                'color' => $color,
            ], [],20*($page+1), 20*$startPage-20);
        }else {
            $products = $productsRepository->findBy([
                'category_id' => $categoriesArr,
                'color' => $color,
            ], ['price'=>$sort], 20*($page+1), 20*$startPage-20);
        }
        return $products;
    }

    private function getColors($products){
        $colors = array();
        foreach ($products as $product){
            $colors[] = $product->getColor();
        }
        return array_unique($colors);

    }

}

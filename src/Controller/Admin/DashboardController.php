<?php

namespace App\Controller\Admin;

use App\Entity\BlackIp;
use App\Entity\Category;
use App\Entity\City;
use App\Entity\CityPages;
use App\Entity\Color;
use App\Entity\Country;
use App\Entity\Products;
use App\Entity\StoneCatalog;
use App\Entity\StoneProduct;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Content;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        //return parent::index();
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
        $url = $routeBuilder->setController(ContentCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Stone Tech');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('css/admin.css')
            ->addCssFile('css/chosen.css')
            ->addCssFile('css/jquery.dm-uploader.css')
            ->addJsFile('js/cache_clear.js')
            ->addJsFile('js/chosen.jquery.min.js')
            ->addJsFile('js/chosen-select.js')
            ->addJsFile('js/bs-custom-file-input.min.js');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::linkToUrl('На сайт','fas fa-home', '/'),
            MenuItem::linkToCrud('Страницы','fa fa-tags', Content::class),
            MenuItem::subMenu('Страницы по городам', 'fa fa-tags')->setSubItems([
                MenuItem::linkToCrud('Города', 'fas fa-list', City::class),
                MenuItem::linkToCrud('Страницы по городам', 'fas fa-list', CityPages::class),
            ]),
            MenuItem::subMenu('Каталог продуктов', 'fas fa-cookie')->setSubItems([
                MenuItem::linkToCrud('Категории', 'fas fa-list', Category::class),
                MenuItem::linkToCrud('Товары', 'fas fa-cookie', Products::class),
                MenuItem::linkToCrud('Цвета', 'fas fa-brush', Color::class),
            ]),
            MenuItem::subMenu('Каталог камня', 'fas fa-cookie')->setSubItems([
                MenuItem::linkToCrud('Категории', 'fas fa-list', StoneCatalog::class),
                MenuItem::linkToCrud('Камень', 'fas fa-cookie', StoneProduct::class),
                MenuItem::linkToCrud('Страна', 'fas fa-list', Country::class),
            ]),
            MenuItem::subMenu('Импорт', 'fas fa-file-import')->setSubItems([
                MenuItem::linktoRoute('Товары', 'fas fa-file-alt', 'admin_product_import'),
                MenuItem::linktoRoute('Обновление цен', 'fas fa-calculator', 'admin_update_prices'),
            ]),
            MenuItem::linkToCrud('Черный список IP','fa fa-tags', BlackIp::class),
        ];

    }
}

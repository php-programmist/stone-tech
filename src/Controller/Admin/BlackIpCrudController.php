<?php

namespace App\Controller\Admin;

use App\Entity\BlackIp;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class BlackIpCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlackIp::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('ip', 'IP адрес'),
        ];
    }

}

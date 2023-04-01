<?php

namespace App\Form;


use App\Entity\Category;
use App\Model\Admin\ProductImport;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('submit', SubmitType::class, ['label'=>'Импортировать'])
            ->add('xlsFile', FileType::class, ['label' => 'Файл xlsx с товарами'])
            ->add('firstRow', NumberType::class, ['label' => 'Начать со строки'])
            ->add('category', EntityType::class, [
                'label'        => 'Категория',
                'class'        => Category::class,
                'attr'         => [
                    'class' => 'chosen',
                ],
            ])
            ->add('baseUri', TextType::class, [
                'label'    => 'Базовый URI',
                'help'     => 'Используется для получения полного URL товара',
                'required' => true,
            ])
            ->add('imagesBig', FileType::class, [
                'label'    => 'zip-файл c изображениями для карточки товаров',
                'required' => false,
            ])
            ->add('imagesCatalog', FileType::class, [
                'label'    => 'zip-файл c изображениями товаров в каталоге',
                'required' => false,
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductImport::class,
            //'allow_extra_fields' => true,
        ]);
    }
}

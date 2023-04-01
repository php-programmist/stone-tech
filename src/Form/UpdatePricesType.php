<?php

namespace App\Form;

use App\Entity\Category;
use App\Model\Admin\UpdatePrices;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatePricesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'label'        => 'Категория',
                'class'        => Category::class,
                'attr'         => [
                    'class' => 'chosen',
                ],
            ])
            ->add('percent', NumberType::class, [
                'label' => 'Процент увеличения цен',
                'help' => 'Чтобы уменьшить цены, укажите отрицательное число'
            ])
            ->add('submit', SubmitType::class, ['label'=>'Изменить цены'])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UpdatePrices::class,
        ]);
    }
}

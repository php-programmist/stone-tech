<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Content;
use App\Model\Admin\UpdatePrices;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityRepository;
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
            ->add('content', EntityType::class, [
                'label'        => 'Раздел',
                'class'        => Content::class,
                'help' => 'Цены будут изменены также в дочерних категориях рекурсивно',
                'attr'         => [
                    'class' => 'chosen',
                ],
                'query_builder' => function (ContentRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->andWhere('c.page_type = :type')
                        ->setParameter('type', 'category')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
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

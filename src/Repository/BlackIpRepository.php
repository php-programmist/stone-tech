<?php

namespace App\Repository;

use App\Entity\BlackIp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlackIp|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlackIp|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlackIp[]    findAll()
 * @method BlackIp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlackIpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlackIp::class);
    }

}
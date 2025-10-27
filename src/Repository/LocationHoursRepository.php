<?php

namespace OHMedia\LocationBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\LocationBundle\Entity\LocationHours;

/**
 * @extends ServiceEntityRepository<LocationHours>
 *
 * @method LocationHours|null find($id, $lockMode = null, $lockVersion = null)
 * @method LocationHours|null findOneBy(array $criteria, array $orderBy = null)
 * @method LocationHours[]    findAll()
 * @method LocationHours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationHoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LocationHours::class);
    }

    //    /**
    //     * @return LocationHours[] Returns an array of LocationHours objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LocationHours
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

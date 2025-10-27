<?php

namespace OHMedia\ContactBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use OHMedia\ContactBundle\Entity\Location;

/**
 * @method Location|null find($id, $lockMode = null, $lockVersion = null)
 * @method Location|null findOneBy(array $criteria, array $orderBy = null)
 * @method Location[]    findAll()
 * @method Location[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function save(Location $location, bool $flush = false): void
    {
        $this->getEntityManager()->persist($location);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Location $location, bool $flush = false): void
    {
        $this->getEntityManager()->remove($location);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPrimary(): ?Location
    {
        return $this->createQueryBuilder('l')
            ->where('l.primary = 1')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllOrdered(): array
    {
        return $this->findBy([], [
            'ordinal' => 'asc',
        ]);
    }
}

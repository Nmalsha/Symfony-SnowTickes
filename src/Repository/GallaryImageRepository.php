<?php

namespace App\Repository;

use App\Entity\GallaryImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GallaryImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method GallaryImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method GallaryImage[]    findAll()
 * @method GallaryImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GallaryImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GallaryImage::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(GallaryImage $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(GallaryImage $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return GallaryImage[] Returns an array of GallaryImage objects
    //  */
    /*
    public function findByExampleField($value)
    {
    return $this->createQueryBuilder('g')
    ->andWhere('g.exampleField = :val')
    ->setParameter('val', $value)
    ->orderBy('g.id', 'ASC')
    ->setMaxResults(10)
    ->getQuery()
    ->getResult()
    ;
    }
     */

    /*
public function findOneBySomeField($value): ?GallaryImage
{
return $this->createQueryBuilder('g')
->andWhere('g.exampleField = :val')
->setParameter('val', $value)
->getQuery()
->getOneOrNullResult()
;
}
 */
}

<?php

namespace App\Repository;

use App\Entity\Attendance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

/**
 * @method Attendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attendance[]    findAll()
 * @method Attendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttendanceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Attendance::class);
    }

    public function findAllLastDay()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->addOrderBy('a.login','ASC')
           ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult()
            ;



//        return $this->createQueryBuilder('a')
//            ->andWhere('a.dateTime > :dateTime')
//            ->setParameter('dateTime', new \DateTime('-48 hours'))
//            ->getQuery()
//            ->getResult()
//        ;

//        return $this->createQueryBuilder('a')
//            ->select('a.id')
//            ->addSelect('a.dateTime')
//            ->andWhere('a.dateTime > :dateTime')
//            ->setParameter('dateTime', new \DateTime('-48 hours'))
//            ->getQuery()
//            ->getResult()
//            ;

    }


    public function findActiveUsersOnSector()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }
        // /**
    //  * @return Attendance[] Returns an array of Attendance objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Attendance
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

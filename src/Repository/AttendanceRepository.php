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

    }

    public function findAllforLastDays($days)
    {
        $dateForQuery = (new \DateTime())->modify("-$days day");
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->setParameter('dateTime', $dateForQuery)
            ->addOrderBy('a.login','ASC')
            ->addOrderBy('a.sector','ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findAllforLastDaysFilteredBySectorOrCompany($days,$sector='',$company='')
    {

        $dateForQuery = (new \DateTime())->modify("-$days day");
//        dump($dateForQuery);
//        dd($days);
        if($company=='lamoda') {
            return $this->createQueryBuilder('a')
                ->andWhere('a.dateTime > :dateTime')
                ->andWhere('a.sector LIKE :sector')
                ->andWhere('a.login NOT LIKE :company')
                ->setParameter('dateTime', $dateForQuery)
                ->setParameter('sector', '%'.$sector.'%')
                ->setParameter('company', '__-'.'%')
                ->addOrderBy('a.login','ASC')
                ->addOrderBy('a.sector','ASC')
                ->addOrderBy('a.dateTime', 'DESC')
                ->getQuery()
                ->getResult()
                ;
        }

        if ($company=='') {
            $company = '%';
        } else {
            $company = $company.'\-'.'%';

        }

        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.sector LIKE :sector')
            ->andWhere('a.login LIKE :company')
            ->setParameter('dateTime', $dateForQuery)
            ->setParameter('sector', '%'.$sector.'%')
            ->setParameter('company', $company)
            ->addOrderBy('a.login','ASC')
            ->addOrderBy('a.sector','ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function findActiveUsersOnSectorInShift($sector, $shift)
    {

        $attendances=$this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.sector = :sector')
            ->andWhere('a.shift = :shift')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->setParameter('sector',$sector)
            ->setParameter('shift', $shift)
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult();

        $attendancesOutput=[];
        $lastLogin ="";
        foreach ($attendances as $attendance ) {
            if ($attendance->getLogin()!=$lastLogin && $attendance->getDirection()=='entrance'){
                $attendancesOutput[]=$attendance;
            }
            $lastLogin=$attendance->getLogin();
        }


        $result = $attendancesOutput;
        return $result;
    }


    public function findActiveUsersOnSector($sector)
    {

        $attendances=$this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.sector = :sector')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->setParameter('sector',$sector)
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult();

        $attendancesOutput=[];
        $lastLogin ="";
        foreach ($attendances as $attendance ) {
            if ($attendance->getLogin()!=$lastLogin && $attendance->getDirection()=='entrance'){
                $attendancesOutput[]=$attendance;
            }
            $lastLogin=$attendance->getLogin();
        }


        $result = $attendancesOutput;
        return $result;
    }

    public function findUsersOnSectorInShift($sector, $shift)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.sector = :sector')
            ->andWhere('a.shift = :shift')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->setParameter('sector',$sector)
            ->setParameter('shift', $shift)
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUsersOnSector($sector)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.sector = :sector')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->setParameter('sector',$sector)
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAttendancesOnSectorInShift($sector, $shift)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.sector = :sector')
            ->andWhere('a.shift = :shift')
            ->setParameter('dateTime', new \DateTime('-14 hours'))
            ->setParameter('sector',$sector)
            ->setParameter('shift',$shift)
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findUsersOnAllSectorsInShift($shift)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.shift = :shift')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->setParameter('shift', $shift)
            ->addOrderBy('a.sector', 'ASC')
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findUsersOnAllSectorsInAllShifts()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->addOrderBy('a.sector', 'ASC')
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllAttendancesWithFinesWithoutApproval()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.fine IS NOT NULL')
            ->andWhere('a.fine_approved IS NULL')
            ->orWhere ('a.sector = :deleted_sector')
            ->setParameter('deleted_sector', 'manually deleted')
            ->addOrderBy('a.sector', 'ASC')
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

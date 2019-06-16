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

//    public function findAllLastDay()
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.dateTime > :dateTime')
//            ->setParameter('dateTime', new \DateTime('-48 hours'))
//            ->addOrderBy('a.login', 'ASC')
//            ->addOrderBy('a.dateTime', 'DESC')
//            ->getQuery()
//            ->getResult();
//
//    }

//    public function findAllforLastDays($days)
//    {
//        $dateForQuery = (new \DateTime())->modify("-$days day");
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.dateTime > :dateTime')
//            ->setParameter('dateTime', $dateForQuery)
//            ->addOrderBy('a.login', 'ASC')
//            ->addOrderBy('a.sector', 'ASC')
//            ->addOrderBy('a.dateTime', 'DESC')
//            ->getQuery()
//            ->getResult();
//    }

    /**
     * *
     * @return Attendance[]
     */
    public function findAllforLastDaysFilteredBySectorOrCompany($dateFrom, $dateTo, $sector = '', $company = '')
    {

        if($company){
            $company=$company.'-';
        }
        if ($company != 'lamoda-') {
            return $this->createQueryBuilder('a')
                ->andWhere('a.dateTime > :dateFrom')
                ->andWhere('a.dateTime <= :dateTo')
                ->andWhere('a.sector LIKE :sector')
                ->andWhere('a.login LIKE :company')
                ->setParameter('dateFrom', $dateFrom)
                ->setParameter('dateTo', $dateTo)
                ->setParameter('sector', '%' . $sector . '%')
                ->setParameter('company', '%' . $company .'%')
                ->addOrderBy('a.login', 'ASC')
                ->addOrderBy('a.sector', 'ASC')
                ->addOrderBy('a.dateTime', 'DESC')
                ->getQuery()
                ->getResult();
        }
        if ($company == 'lamoda-') {
            return $this->createQueryBuilder('a')
                ->andWhere('a.dateTime > :dateFrom')
                ->andWhere('a.dateTime <= :dateTo')
                ->andWhere('a.sector LIKE :sector')
                ->andWhere('a.login NOT LIKE :company')
                ->setParameter('dateFrom', $dateFrom)
                ->setParameter('dateTo', $dateTo)
                ->setParameter('sector', '%' . $sector . '%')
                ->setParameter('company', '%' . '-' . '%')
                ->addOrderBy('a.login', 'ASC')
                ->addOrderBy('a.sector', 'ASC')
                ->addOrderBy('a.dateTime', 'DESC')
                ->getQuery()
                ->getResult();
        }
    }

//    public function findActiveUsersOnSectorInShift($sector, $shift)
//    {
//
//        $attendances = $this->createQueryBuilder('a')
//            ->andWhere('a.dateTime > :dateTime')
//            ->andWhere('a.sector = :sector')
//            ->andWhere('a.shift = :shift')
//            ->setParameter('dateTime', new \DateTime('-48 hours'))
//            ->setParameter('sector', $sector)
//            ->setParameter('shift', $shift)
//            ->addOrderBy('a.login', 'ASC')
//            ->addOrderBy('a.dateTime', 'DESC')
//            ->getQuery()
//            ->getResult();
//
//        $attendancesOutput = [];
//        $lastLogin = "";
//        foreach ($attendances as $attendance) {
//            if ($attendance->getLogin() != $lastLogin && $attendance->getDirection() == 'entrance') {
//                $attendancesOutput[] = $attendance;
//            }
//            $lastLogin = $attendance->getLogin();
//        }
//
//
//        $result = $attendancesOutput;
//        return $result;
//    }


    public function findActiveUsersOnSector($sector)
    {

        $attendances = $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.sector = :sector')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->setParameter('sector', $sector)
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult();

        $attendancesOutput = [];
        $lastLogin = "";
        foreach ($attendances as $attendance) {
            if ($attendance->getLogin() != $lastLogin && $attendance->getDirection() == 'entrance') {
                $attendancesOutput[] = $attendance;
            }
            $lastLogin = $attendance->getLogin();
        }


        $result = $attendancesOutput;
        return $result;
    }

//    public function findUsersOnSectorInShift($sector, $shift)
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.dateTime > :dateTime')
//            ->andWhere('a.sector = :sector')
//            ->andWhere('a.shift = :shift')
//            ->setParameter('dateTime', new \DateTime('-48 hours'))
//            ->setParameter('sector', $sector)
//            ->setParameter('shift', $shift)
//            ->addOrderBy('a.login', 'ASC')
//            ->addOrderBy('a.dateTime', 'DESC')
//            ->getQuery()
//            ->getResult();
//    }

    public function findUsersOnSector($sector)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.sector = :sector')
            ->setParameter('dateTime', new \DateTime('-48 hours'))
            ->setParameter('sector', $sector)
            ->addOrderBy('a.dateTime', 'DESC')
            ->addOrderBy('a.login', 'ASC')
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
            ->setParameter('sector', $sector)
            ->setParameter('shift', $shift)
            ->addOrderBy('a.login', 'ASC')
            ->addOrderBy('a.dateTime', 'ASC')
            ->getQuery()
            ->getResult();
    }


//    public function findUsersOnAllSectorsInShift($shift)
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.dateTime > :dateTime')
//            ->andWhere('a.shift = :shift')
//            ->setParameter('dateTime', new \DateTime('-48 hours'))
//            ->setParameter('shift', $shift)
//            ->addOrderBy('a.sector', 'ASC')
//            ->addOrderBy('a.login', 'ASC')
//            ->addOrderBy('a.dateTime', 'DESC')
//            ->getQuery()
//            ->getResult();
//    }

    public function findAllActiveWorkers()
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

//    public function findUsersOnAllSectorsInAllShifts()
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.dateTime > :dateTime')
//            ->setParameter('dateTime', new \DateTime('-48 hours'))
//            ->addOrderBy('a.sector', 'ASC')
//            ->addOrderBy('a.login', 'ASC')
//            ->addOrderBy('a.dateTime', 'DESC')
//            ->getQuery()
//            ->getResult();
//    }

    public function findAllAttendancesWithFinesWithoutApproval()
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.fine IS NOT NULL')
            ->andWhere('a.fine_approved IS NULL')
            ->orWhere('a.sector = :deleted_sector')
            ->setParameter('deleted_sector', 'manually deleted')
            ->addOrderBy('a.dateTime', 'DESC')
            ->addOrderBy('a.sector', 'ASC')
            ->addOrderBy('a.login', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findAllAttendancesWithFines($days, $sector, $fine)
    {
        $dateForQuery = (new \DateTime())->modify("-$days day");
        return $this->createQueryBuilder('a')
            ->andWhere('a.fine IS NOT NULL')
            ->andWhere('a.dateTime > :dateTime')
            ->andWhere('a.sector LIKE :sector')
            ->andWhere('a.fine LIKE :fine')
            ->setParameter('dateTime', $dateForQuery)
            ->setParameter('sector', '%' . $sector . '%')
            ->setParameter('fine', '%' . $fine . '%')
            ->addOrderBy('a.dateTime', 'DESC')
            ->getQuery()
            ->getResult();

    }

}

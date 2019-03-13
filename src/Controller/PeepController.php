<?php


namespace App\Controller;


use App\Entity\Attendance;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PeepController extends AbstractController
{

    /**
     * @Route("/peepInterface", name="peepInterface")
     */
    public function PeepInterface()
    {
        $this->denyAccessUnlessGranted('ROLE_PEEP');

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendances = $attendanceRepo->findUsersOnAllSectorsInShift($this->getUser()->getShift());
        $attendancesInPeepSector = $attendanceRepo->findUsersOnSectorInShift($this->getUser()->getSector(),$this->getUser()->getShift());

        $attendancesOutput=[];
        $lastLogin ="";
        foreach ($attendancesInPeepSector as $attendance ) {
            if ($attendance->getLogin()!=$lastLogin && $attendance->getDirection()=='entrance'){
                $attendancesOutput[]=$attendance;
            }
            $lastLogin=$attendance->getLogin();
        }


        //dd($attendances);
        $attendancesOutput=[];
        $lastLogin ="";
        $usersInSectorQty=[];
        foreach (USER::SECTORS_LIST as $sector) {
            $usersInSectorQty[$sector]['total']=0;
            foreach (USER::PROVIDERS_LIST as $key=>$value){
                $usersInSectorQty[$sector][$key]=0;
            }
            $usersInSectorQty[$sector]['lamoda']=0;
        }

//        dd($usersInSectorQty);
        //dump($attendances);
        foreach ($attendances as $attendance ) {
            if ($attendance->getLogin()!=$lastLogin && $attendance->getDirection()=='entrance'){
                $attendancesOutput[]=$attendance;
                $usersInSectorQty[$attendance->getSector()]['total']++;
                if(substr($attendance->getLogin(),2,1)=='_'){
                    $providerPerfix= substr($attendance->getLogin(),0,2);
                    $usersInSectorQty[$attendance->getSector()][$providerPerfix]++;
                } else {
                    $usersInSectorQty[$attendance->getSector()]['lamoda']++;
                }
            }
            $lastLogin=$attendance->getLogin();
        }

        //dump($attendancesOutput);
        $attendancesWithFinesWithoutApproval=[];
        $attendancesWithFinesWithoutApproval=$attendanceRepo->findAllAttendancesWithFinesWithoutApproval();


        //dd($attendances);
        return $this->render('Peep/PeepInterface.html.twig', [
            'sectors'=>USER::SECTORS_LIST,
            'attendances'=>$attendancesOutput,
            'usersInSectors'=>$usersInSectorQty,
            'attendancesForApproval'=>$attendancesWithFinesWithoutApproval,
            ]);
    }


}
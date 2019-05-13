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
        $attendancesInPeepSector = $attendanceRepo->findUsersOnSector($this->getUser()->getSector());

        $attendancesInPeepSectorOutput=[];
        foreach ($attendancesInPeepSector as $attendance ) {
            if (!isset($attendancesInPeepSectorOutput[$attendance->getLogin()])){
                $attendancesInPeepSectorOutput[$attendance->getLogin()]=$attendance;
            }
        }

//        $attendancesInPeepSector = $attendanceRepo->findUsersOnSectorInShift($this->getUser()->getSector(),$this->getUser()->getShift());
//
//
//        $attendancesInPeepSectorOutput=[];
//        $lastLogin ="";
//        foreach ($attendancesInPeepSector as $attendance ) {
//            if ($attendance->getLogin()!=$lastLogin && $attendance->getDirection()=='entrance'){
//                $attendancesInPeepSectorOutput[]=$attendance;
//            }
//            $lastLogin=$attendance->getLogin();
//        }

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

        foreach ($attendances as $attendance ) {  //TODO дублируется в админконтроллере, нужно вынести в 1 место
            if ($attendance->getLogin()!=$lastLogin && $attendance->getDirection()=='entrance'){
                $attendancesOutput[]=$attendance;
                if(isset($usersInSectorQty[$attendance->getSector()]['total'])){
                    $usersInSectorQty[$attendance->getSector()]['total']++;
                }
                if(substr($attendance->getLogin(),2,1)=='-'){
                    $providerPerfix= substr($attendance->getLogin(),0,2);
                    if(array_key_exists($providerPerfix, USER::PROVIDERS_LIST)) {
                        $usersInSectorQty[$attendance->getSector()][$providerPerfix]++;  //TODO если неизвестный перфикс то крах, надо переделать. Пофиксил но вообще каша, надо переделать.
                    } else {
                        if(isset($usersInSectorQty[$attendance->getSector()]['lamoda'])){
                            $usersInSectorQty[$attendance->getSector()]['lamoda']++;
                        }
                    }
                } else {
                    if(isset($usersInSectorQty[$attendance->getSector()]['lamoda'])){
                        $usersInSectorQty[$attendance->getSector()]['lamoda']++;
                    }
                }
            }
            $lastLogin=$attendance->getLogin();
        }

        $attendancesWithFinesWithoutApproval=$attendanceRepo->findAllAttendancesWithFinesWithoutApproval();


        //dd($attendances);
        return $this->render('Peep/PeepInterface.html.twig', [
            'attendances'=>$attendancesInPeepSectorOutput,
            'usersInSectors'=>$usersInSectorQty,
            'attendancesForApproval'=>$attendancesWithFinesWithoutApproval,
            ]);
    }


}
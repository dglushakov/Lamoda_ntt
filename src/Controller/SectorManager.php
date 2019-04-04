<?php


namespace App\Controller;

use App\Entity\Attendance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class SectorManager extends AbstractController
{
    /**
     * @Route("/SectorManagerWorkSpace", name="SMworkInterface")
     *
     */
    public function WorkInterface()
    {
        $this->denyAccessUnlessGranted('ROLE_SECTOR_MANAGER');
        // $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
//        $attendances = $attendanceRepo->findAll();
        //$attendances = $attendanceRepo->findUsersOnSectorInShift($this->getUser()->getSector(),$this->getUser()->getShift());
        //$attendances = $attendanceRepo->findUsersOnSector($this->getUser()->getSector());
        $attendances = $attendanceRepo->findUsersOnSectorExceptManuallyDeleted($this->getUser()->getSector());
        //TODO использовать другую выборку

        $attendancesOutput=[];
        $lastLogin ="";
        foreach ($attendances as $attendance ) {
            if ($attendance->getLogin()!=$lastLogin && $attendance->getDirection()=='entrance'){
                $attendancesOutput[]=$attendance;
            }
           $lastLogin=$attendance->getLogin();
       }


        $result = $this->render('SectorManager/SectorManagerInterface.html.twig', [
            'attendances' => $attendancesOutput,
            'peopleCounter' => count($attendancesOutput),
        ]);
        return $result;
    }


}
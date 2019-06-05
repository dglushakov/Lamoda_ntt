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
        $attendancesInPeepSector = $attendanceRepo->findUsersOnSector($this->getUser()->getSector());

        $attendancesInPeepSectorOutput=[];
        foreach ($attendancesInPeepSector as $attendance ) {
            if (!isset($attendancesInPeepSectorOutput[$attendance->getLogin()])){
                $attendancesInPeepSectorOutput[$attendance->getLogin()]=$attendance;
            }
        }

        $attendancesWithFinesWithoutApproval=$attendanceRepo->findAllAttendancesWithFinesWithoutApproval();

        return $this->render('Peep/PeepInterface.html.twig', [
            'attendances'=>$attendancesInPeepSectorOutput,
            'attendancesForApproval'=>$attendancesWithFinesWithoutApproval,
            ]);
    }


}
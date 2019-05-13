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

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendances = $attendanceRepo->findUsersOnSector($this->getUser()->getSector());

        $attendancesOutput=[];

        foreach ($attendances as $attendance ) {
            if (!isset($attendancesOutput[$attendance->getLogin()])){
                $attendancesOutput[$attendance->getLogin()]=$attendance;
            }
        }

        //dd($attendancesOutput);
        $result = $this->render('SectorManager/SectorManagerInterface.html.twig', [
            'attendances' => $attendancesOutput,
        ]);
        return $result;
    }


}
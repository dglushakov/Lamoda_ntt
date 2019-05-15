<?php


namespace App\Controller\Reports;

use App\Controller\Reports\Form\finesReportFiltersForm;
use App\Entity\User;
use App\Controller\Reports\Form\FiltersForm;
use App\Entity\Attendance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends AbstractController
{

    /**
     * @Route("/reports/", name="reports")
     *
     */
    public function mainReport(Request $request){
    $reportDepthInDays=14;

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);

        $filtersForm = $this->createForm(FiltersForm::class, NULL);
        $filtersForm->handleRequest($request);
        if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {

            $formData = $filtersForm->getData();
            $provider = array_search($formData['Provider'], USER::PROVIDERS_LIST );
            if($formData['Depth']) {
                $reportDepthInDays=$formData['Depth'];
            }
            if($formData['Depth']=='' || !$formData['Depth']) {
                $formData['Depth']=$reportDepthInDays;
            }

            $attendances = $attendanceRepo->findAllforLastDaysFilteredBySectorOrCompany($formData['Depth'],$formData['Sector'],$provider);

        } else {

            $attendances = $attendanceRepo->findAllforLastDaysFilteredBySectorOrCompany($reportDepthInDays);
        }

        //dd($reportDepthInDays);
        //dump($attendances);
        $workTime=[];
        for($i=0; $i<count($attendances)-1; $i++) {
            if
            (
                ($attendances[$i]->getDirection()=='exit' && $attendances[$i+1]->getDirection()=='entrance')
                && ($attendances[$i]->getLogin()===$attendances[$i+1]->getLogin())
                && ($attendances[$i]->getSector()===$attendances[$i+1]->getSector())
            )

            {
                $entrance = $attendances[$i+1];
                $exit = $attendances[$i];
//                    dump($entrance);
//                    dd($exit);
                $day = $entrance->getDateTime()->format('d.m.Y');
                $shift = 1;

                if($entrance->getDateTime()->format('H') >= 20)
                {
                    $shift = 2;
                } elseif ($entrance->getDateTime()->format('H') < 8)
                {

                    $shift = 2;
                    $day1 = clone $entrance->getDateTime();
                    $day1 ->sub(new \DateInterval('P1D'));
                    //$day1->sub(new \DateInterval('P1D'));
                    $day = $day1->format('d.m.Y');
                    $currentWorkPeriod = $exit->getDateTime()->getTimestamp() - $entrance->getDateTime()->getTimestamp();

                }

                $currentWorkPeriod = $exit->getDateTime()->getTimestamp() - $entrance->getDateTime()->getTimestamp();


                if (isset($workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$day][$shift])){
                    $workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$day][$shift] =
                        $workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$day][$shift]
                        + $currentWorkPeriod;
                } else {
                    $workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$day][$shift] = $currentWorkPeriod;
                }

            }

        }


        return $this->render('/Reports/reports.html.twig',[
            'filtersForm'=>$filtersForm->createView(),
            'attendances'=>$attendances,
            'worktime'=>$workTime,
            'depth'=>$reportDepthInDays,
        ]);
    }


    /**
     * @Route("/sectorManagerReport/", name="sectorManagerReport")
     *
     */
    public function sectorManagerReport(){


        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);

        $attendances = $attendanceRepo->findAttendancesOnSectorInShift($this->getUser()->getSector(), $this->getUser()->getShift());

        $attendancesOutput=[];
        foreach ($attendances as $attendance){
            /** @var Attendance $attendance */
                $attendancesOutput[$attendance->getLogin()][] = [
                    'direction'=>$attendance->getDirection(),
                    'time'=>$attendance->getDateTime()
                ];
        }




        return $this->render('/Reports/sectorMangerReport.html.twig', [
            'attendances'=>$attendances,
            'attendancesOutput'=>$attendancesOutput,
        ]);
    }


    /**
     * @Route("/finesReport", name="finesreport")
     */
    public function finesReport(Request $request){
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);

        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        $sectorManagres= $usersRepo->findAllSectorManagers();

        $days=14;
        $sector='';
        $fine='';
        //$fines = $attendanceRepo->findAllAttendancesWithFines($days);

        $filtersForm = $this->createForm(finesReportFiltersForm::class, NULL);
        $filtersForm->handleRequest($request);
        if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {

            $formData = $filtersForm->getData();
            if($formData['Depth']) {
                $days=$formData['Depth'];
            }
            $sector = array_search($formData['Sector'], USER::SECTORS_LIST );
            $fine = array_search($formData['Fine'], ATTENDANCE::FINES );

        }


        $fines = $attendanceRepo->findAllAttendancesWithFines($days, $sector, $fine);
       // $fines = $attendanceRepo->findAllAttendancesWithFines($days);

        return $this->render('/Reports/finesReport.html.twig',[
            'fines'=> $fines,
            'sectorManagers'=> $sectorManagres,
            'filtersForm'=>$filtersForm->createView(),
        ]);
    }
}
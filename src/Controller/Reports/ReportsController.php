<?php


namespace App\Controller\Reports;

use App\Controller\Reports\Form\finesReportFiltersForm;
use App\Entity\User;
use App\Controller\Reports\Form\FiltersForm;
use App\Entity\Attendance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends AbstractController
{

    /**
     * @Route("/workTimeReport/", name="workTimeReport")
     *
     */
    public function workTimeReport(Request $request)
    {
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);

        $filtersForm = $this->createForm(FiltersForm::class, NULL);
        $filtersForm->handleRequest($request);

        $dateFrom = new \DateTime();
        $dateTo = new \DateTime();
        $sector = '';
        $provider = '';
        if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {
            $formData = $filtersForm->getData();
            $dateFrom = $formData['dateFrom'];
            $dateTo = $formData['dateTo'];
            $sector = $formData['Sector'];
            if ($formData['Provider']) {
                $provider = array_search($formData['Provider'], USER::PROVIDERS_LIST);
            }


        }
        $dateTo->add(new \DateInterval('PT23H59M59S'));

        $attendances = $attendanceRepo->findAllforLastDaysFilteredBySectorOrCompany($dateFrom, $dateTo, $sector, $provider);

        dump($attendances);
        $daysCount = $dateFrom->diff($dateTo)->d;
        $workTime = [];
        for ($i = 0; $i < count($attendances) - 1; $i++) {

            if (!isset($workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()])) {
                $adyForArray = clone $dateFrom;
            for($day=0; $day<=$daysCount; $day++){

                $workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$adyForArray->format('d.m.Y')][1]=0;
                $workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$adyForArray->format('d.m.Y')][2]=0;
                $adyForArray->modify("+1 Days");
            }
        }
            if
            (
                ($attendances[$i]->getDirection() == 'exit' && $attendances[$i + 1]->getDirection() == 'entrance')
                && ($attendances[$i]->getLogin() === $attendances[$i + 1]->getLogin())
                && ($attendances[$i]->getSector() === $attendances[$i + 1]->getSector())
            ) {

                $entrance = $attendances[$i + 1];
                $exit = $attendances[$i];
                dump($entrance);
                $currentDateTime = clone $entrance->getDateTime();

                do{
                    $shiftEnd= clone $currentDateTime;

                    if($currentDateTime->format('H')>=0 && $currentDateTime->format('H')<8) {
                        $shiftEnd->setTime(7,59,59);
                        $shift = 1;
                    }

                    if($currentDateTime->format('H')>=8 && $currentDateTime->format('H')<20) {
                        $shiftEnd->setTime(19,59,59);
                        $shift = 1;
                    }

                    if($currentDateTime->format('H')>=20 && $currentDateTime->format('H')<=23) {
                        $shiftEnd->setTime(7,59,59);
                        $shiftEnd->modify('+1 Day');
                        $shift = 2;
                    }

                    if($exit->getDateTime() < $shiftEnd) {
                        $shiftEnd = clone $exit->getDateTime();
                    }

                    $day =$currentDateTime->format('d.m.Y');

                    if ($shiftEnd < $exit->getDateTime()) {
                        $currentWorkPeriod = $shiftEnd->getTimestamp() - $currentDateTime->getTimestamp()+1;
                    } else {
                        $currentWorkPeriod = $exit->getDateTime()->getTimestamp() - $currentDateTime->getTimestamp() ;
                    }

                    $workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$day][$shift]+=$currentWorkPeriod;
                    $currentDateTime=clone $shiftEnd;
                    $currentDateTime->modify('+1 sec');

                }
                    While($currentDateTime <= $exit->getDateTime());
            }
        }


        return $this->render('/Reports/workTimeReport.html.twig', [
            'filtersForm' => $filtersForm->createView(),
            'attendances' => $attendances,
            'worktime' => $workTime,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

//    /**
//     * @Route("/reports/", name="reports")
//     *
//     */
//    public function mainReport(Request $request)
//    {
//        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
//
//        $filtersForm = $this->createForm(FiltersForm::class, NULL);
//        $filtersForm->handleRequest($request);
//
//        $dateFrom = new \DateTime();
//        $dateTo = new \DateTime();
//        $sector = '';
//        $provider = '';
//        if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {
//            $formData = $filtersForm->getData();
//            $dateFrom = $formData['dateFrom'];
//            $dateTo = $formData['dateTo'];
//            $sector = $formData['Sector'];
//            if ($formData['Provider']) {
//                $provider = array_search($formData['Provider'], USER::PROVIDERS_LIST);
//            }
//
//
//        }
//
//
//        $dateTo->add(new \DateInterval('PT23H59M59S'));
//
//        $attendances = $attendanceRepo->findAllforLastDaysFilteredBySectorOrCompany($dateFrom, $dateTo, $sector, $provider);
//
//        $workTime = [];
//        for ($i = 0; $i < count($attendances) - 1; $i++) {
//            if
//            (
//                ($attendances[$i]->getDirection() == 'exit' && $attendances[$i + 1]->getDirection() == 'entrance')
//                && ($attendances[$i]->getLogin() === $attendances[$i + 1]->getLogin())
//                && ($attendances[$i]->getSector() === $attendances[$i + 1]->getSector())
//            ) {
//                $entrance = $attendances[$i + 1];
//                $exit = $attendances[$i];
////                    dump($entrance);
////                    dd($exit);
//                $day = $entrance->getDateTime()->format('d.m.Y');
//                $shift = 1;
//
//                if ($entrance->getDateTime()->format('H') >= 20) {
//                    $shift = 2;
//                } elseif ($entrance->getDateTime()->format('H') < 8) {
//
//                    $shift = 2;
//                    $day1 = clone $entrance->getDateTime();
//                    $day1->sub(new \DateInterval('P1D'));
//                    //$day1->sub(new \DateInterval('P1D'));
//                    $day = $day1->format('d.m.Y');
//                    $currentWorkPeriod = $exit->getDateTime()->getTimestamp() - $entrance->getDateTime()->getTimestamp();
//
//                }
//
//                $currentWorkPeriod = $exit->getDateTime()->getTimestamp() - $entrance->getDateTime()->getTimestamp();
//
//
//                if (isset($workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$day][$shift])) {
//                    $workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$day][$shift] =
//                        $workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$day][$shift]
//                        + $currentWorkPeriod;
//                } else {
//                    $workTime[$attendances[$i]->getLogin()][$attendances[$i]->getSector()][$day][$shift] = $currentWorkPeriod;
//                }
//
//            }
//
//        }
//
//
//        return $this->render('/Reports/reports.html.twig', [
//            'filtersForm' => $filtersForm->createView(),
//            'attendances' => $attendances,
//            'worktime' => $workTime,
//            'dateFrom' => $dateFrom,
//            'dateTo' => $dateTo,
//        ]);
//    }


    /**
     * @Route("/sectorManagerReport/", name="sectorManagerReport")
     *
     */
    public function sectorManagerReport()
    {


        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);

        $attendances = $attendanceRepo->findAttendancesOnSectorInShift($this->getUser()->getSector(), $this->getUser()->getShift());

        $attendancesOutput = [];
        foreach ($attendances as $attendance) {
            /** @var Attendance $attendance */
            $attendancesOutput[$attendance->getLogin()][] = [
                'direction' => $attendance->getDirection(),
                'time' => $attendance->getDateTime()
            ];
        }


        return $this->render('/Reports/sectorMangerReport.html.twig', [
            'attendances' => $attendances,
            'attendancesOutput' => $attendancesOutput,
        ]);
    }


    /**
     * @Route("/finesReport", name="finesreport")
     */
    public function finesReport(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_FINES_REPORT');
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);

        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        $sectorManagres = $usersRepo->findAllSectorManagers();

        $days = 14;
        $sector = '';
        $fine = '';
        //$fines = $attendanceRepo->findAllAttendancesWithFines($days);

        $filtersForm = $this->createForm(finesReportFiltersForm::class, NULL);
        $filtersForm->handleRequest($request);
        if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {

            $formData = $filtersForm->getData();
            if ($formData['Depth']) {
                $days = $formData['Depth'];
            }
            $sector = array_search($formData['Sector'], USER::SECTORS_LIST);
            $fine = array_search($formData['Fine'], ATTENDANCE::FINES);

        }


        $fines = $attendanceRepo->findAllAttendancesWithFines($days, $sector, $fine);
        // $fines = $attendanceRepo->findAllAttendancesWithFines($days);

        return $this->render('/Reports/finesReport.html.twig', [
            'fines' => $fines,
            'sectorManagers' => $sectorManagres,
            'filtersForm' => $filtersForm->createView(),
        ]);
    }
}
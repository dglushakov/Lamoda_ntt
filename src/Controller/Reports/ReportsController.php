<?php


namespace App\Controller\Reports;

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
    $reportDepthInDays=365;

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendances = $attendanceRepo->findAll();
        $attendances = $attendanceRepo->findAllforLastDays($reportDepthInDays);
        dd($attendances);


        $filtersForm = $this->createForm(FiltersForm::class, NULL);
        $filtersForm->handleRequest($request);
        if ($filtersForm->isSubmitted() && $filtersForm->isValid()) {
            //dd($filtersForm->getData());
            $formData = $filtersForm->getData();
            $criteria = [];
            if ($formData['Sector']) {
                $criteria['sector'] = $formData['Sector'];
            }
            if ($formData['Provider']) {
                //$criteria['provider'] = $formData['Provider'];
            }



            $attendances = $attendanceRepo->findBy($criteria);

        }

//        return new Response("REPORTS!");
        return $this->render('/Reports/reports.html.twig',[
            'filtersForm'=>$filtersForm->createView(),
            'attendances'=>$attendances,
            'depth'=>$reportDepthInDays,
        ]);
    }
}
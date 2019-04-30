<?php


namespace App\Controller\Reports;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends AbstractController
{

    /**
     * @Route("/reports/", name="reports")
     *
     */
    public function mainReport(){

//        return new Response("REPORTS!");
        return $this->render('/Reports/reports.html.twig',[

        ]);
    }
}
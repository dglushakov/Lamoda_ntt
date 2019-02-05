<?php


namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class SectorManager extends AbstractController
{
    /**
     * @Route("/SectorManagerWorkSpace", name="SMworkInterface")
     */
    public function WorkInterface(){

        //return new Response("WorkSpace");
        $result = $this->render('SectorManager/SectorManagerInterface.html.twig');
        return $result;
    }


    /**
     * @Route("/attendance/new/{login}", name ="AddAttendance")
     */
    public function AddAttendance() {
        
    }

}
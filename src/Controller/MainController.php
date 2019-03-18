<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_home", schemes={"%secure_channel%"})
     *
     */
    public function home()
    {

        return $this->redirectToRoute('app_login');
    }

}
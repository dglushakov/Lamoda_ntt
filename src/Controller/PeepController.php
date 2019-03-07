<?php


namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PeepController extends AbstractController
{

    /**
     * @Route("/peepInterface", name="peepInterface")
     */
    public function PeepInterface()
    {
        $this->denyAccessUnlessGranted('ROLE_PEEP');

        return $this->render('Peep/PeepInterface.html.twig');
    }

}
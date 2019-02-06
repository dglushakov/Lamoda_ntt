<?php


namespace App\Controller;

use App\Entity\Attendance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SectorManager extends AbstractController
{
    /**
     * @Route("/SectorManagerWorkSpace", name="SMworkInterface")
     *
     */
    public function WorkInterface(){

       // $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        //$attendances = $attendanceRepo->findAll();
        $attendances = $attendanceRepo->findBy( [],['dateTime'=>'DESC'],10);

        $result = $this->render('SectorManager/SectorManagerInterface.html.twig', [
            'attendances'=>$attendances,

        ]);
        return $result;
    }


    /**
     * @Route("/attendance/new/{login}", name ="AddAttendance")
     */
    public function AddAttendance($login) {
        $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $lastLoginAttendance = $attendanceRepo->findOneBy(['login'=>$login], ['dateTime'=>'DESC']);

        $attendance = new Attendance();
        $attendance->setLogin($login);
        $attendance->setDateTime(new \DateTime());

        if ($lastLoginAttendance && $lastLoginAttendance->getDirection()!='exit'){
            $attendance->setDirection('exit');
        } else {
            $attendance->setDirection('entrance');
        }


        $entityManager->persist($attendance);
        $entityManager->flush();

        //return new Response('Saved new product with id '.$attendance->getId());
        return $this->redirectToRoute('SMworkInterface');
    }

    /**
     * @Route("/editcomment/{attendanceId}/{text}")
     */
    public function EditAttendanceComment($attendanceId, $text=null){
        $entityManager = $this->getDoctrine()->getManager();

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendance = $attendanceRepo->find($attendanceId);
        $attendance->setComment($text);

        $entityManager->persist($attendance);
        $entityManager->flush();

        return $this->redirectToRoute('SMworkInterface');
    }

    /**
     * @Route("/editfine/{attendanceId}/{action}")
     */
    public function EditFine($attendanceId, $action=null){
        $entityManager = $this->getDoctrine()->getManager();

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendance = $attendanceRepo->find($attendanceId);
        $attendance->setFine($action);

        $entityManager->persist($attendance);
        $entityManager->flush();

        return $this->redirectToRoute('SMworkInterface');
    }

    /**
     * @Route("/addcounter")
     */
    public function addCounter() {


        return new JsonResponse(['counter' => rand(5, 100)]);
    }

}
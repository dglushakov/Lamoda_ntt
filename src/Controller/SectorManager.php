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
        // $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
//        $attendances = $attendanceRepo->findAll();
        $attendances = $attendanceRepo->findAllLastDay();



        $attendancesOutput=[];
        $lastLogin ="";
        foreach ($attendances as $attendance ) {
            if ($attendance->getLogin()!=$lastLogin && $attendance->getDirection()=='entrance'){
                $attendancesOutput[]=$attendance;
            }
           $lastLogin=$attendance->getLogin();
       }


        $result = $this->render('SectorManager/SectorManagerInterface.html.twig', [
            'attendances' => $attendancesOutput,
            'peopleCounter' => count($attendancesOutput),
        ]);
        return $result;
    }




    /**
     * @Route("/attendance/new/{login}", name ="AddAttendance")
     */
    public function AddAttendance($login)
    {
        $this->denyAccessUnlessGranted('ROLE_SECTOR_MANAGER');
        $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $lastLoginAttendance = $attendanceRepo->findOneBy(['login' => $login], ['dateTime' => 'DESC']);

        $login = trim($login);
        $attendance = new Attendance();
        $attendance->setLogin($login);
        $attendance->setDateTime(new \DateTime());
        $attendance->setShift($this->getUser()->getShift());
        $attendance->setSector($this->getUser()->getSector());

        if ($lastLoginAttendance && $lastLoginAttendance->getDirection() != 'exit') {
            $attendance->setDirection('exit');

        } else {
            $attendance->setDirection('entrance');

        }

        $entityManager->persist($attendance);
        $entityManager->flush();

        //return new Response('Saved new product with id '.$attendance->getId());

        //TODO изменение счетчика
        return $this->redirectToRoute('SMworkInterface');
    }

    /**
     * @Route("/editcomment/{attendanceId}/{text}")
     */
    public function EditAttendanceComment($attendanceId, $text = null)
    {
        $this->denyAccessUnlessGranted('ROLE_SECTOR_MANAGER');
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
    public function EditFine($attendanceId, $action = null)
    {
        $this->denyAccessUnlessGranted('ROLE_SECTOR_MANAGER');
        $entityManager = $this->getDoctrine()->getManager();

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendance = $attendanceRepo->find($attendanceId);
        $attendance->setFine($action);

        $entityManager->persist($attendance);
        $entityManager->flush();

        return $this->redirectToRoute('SMworkInterface');
    }


//    /**
//     * @Route("/resetPeopleCounter", name ="resetPeopleCounter")
//     */
//    public function resetPeopleCounter()
//    {
//        $this->denyAccessUnlessGranted('ROLE_SECTOR_MANAGER');
//        $file_counter = "counter.txt";
//        $fp = fopen($file_counter, "w");
//        fwrite($fp, "0");
//        return $this->redirectToRoute('SMworkInterface');
//    }
}
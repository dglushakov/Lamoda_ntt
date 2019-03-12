<?php


namespace App\Controller;

use App\Entity\Attendance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;


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
        $attendances = $attendanceRepo->findUsersOnSectorInShift($this->getUser()->getSector(),$this->getUser()->getShift());

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
        $lastLoginAttendance = $attendanceRepo->findOneBy(['login' => $login, 'sector'=>$this->getUser()->getSector(),], ['dateTime' => 'DESC']);

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

    /**
     * @Route("/attendance/delete/{attendanceId}")
     */
    public function deleteAttendance($attendanceId)
    {
        $this->denyAccessUnlessGranted('ROLE_SECTOR_MANAGER');
        $entityManager = $this->getDoctrine()->getManager();

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendance = $attendanceRepo->find($attendanceId);
        $attendance->setSector("manually deleted");

        $entityManager->persist($attendance);
        $entityManager->flush();

        return $this->redirectToRoute('SMworkInterface');
    }

}
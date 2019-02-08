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

        // $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        //$attendances = $attendanceRepo->findAll();
        $attendances = $attendanceRepo->findBy([], ['dateTime' => 'DESC'], 1010);


        $file_counter = "counter.txt";
        if (file_exists($file_counter) && filesize($file_counter) != 0) {
            $fp = fopen($file_counter, "r");
            $peopleCounter = fread($fp, filesize($file_counter));
            fclose($fp);
        } else {
            $fp = fopen($file_counter, "w");
            fwrite($fp, "0");
            fclose($fp);
            $peopleCounter = 0;
        }


        $result = $this->render('SectorManager/SectorManagerInterface.html.twig', [
            'attendances' => $attendances,
            'peopleCounter' => $peopleCounter,
        ]);
        return $result;
    }


    /**
     * @Route("/attendance/new/{login}", name ="AddAttendance")
     */
    public function AddAttendance($login)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $lastLoginAttendance = $attendanceRepo->findOneBy(['login' => $login], ['dateTime' => 'DESC']);

        $login = trim($login);
        $attendance = new Attendance();
        $attendance->setLogin($login);
        $attendance->setDateTime(new \DateTime());

        $file_counter = "counter.txt";
        $fp = fopen($file_counter, "r+");
        $counter = (int)fread($fp, filesize($file_counter));
        fclose($fp);

        if ($lastLoginAttendance && $lastLoginAttendance->getDirection() != 'exit') {
            $attendance->setDirection('exit');
            $counter--;
        } else {
            $attendance->setDirection('entrance');
            $counter++;
        }
        $fp = fopen($file_counter, "w");
        fwrite($fp, $counter);
        fclose($fp);


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
        $entityManager = $this->getDoctrine()->getManager();

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendance = $attendanceRepo->find($attendanceId);
        $attendance->setFine($action);

        $entityManager->persist($attendance);
        $entityManager->flush();

        return $this->redirectToRoute('SMworkInterface');
    }


    /**
     * @Route("/resetPeopleCounter", name ="resetPeopleCounter")
     */
    public function resetPeopleCounter()
    {

        $file_counter = "counter.txt";
        $fp = fopen($file_counter, "w");
        fwrite($fp, "0");
        return $this->redirectToRoute('SMworkInterface');
    }
}
<?php


namespace App\Controller\Attendances;


use App\Entity\Attendance;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AttendanceController extends AbstractController
{
    /**
     * @Route("/attendance/new/{login}", name ="AddAttendance")
     */
    public function AddAttendance($login)
    {
        $this->denyAccessUnlessGranted('ROLE_ATTENDANCE_CREATE');


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


        $user = $this->getUser();
        $userRoles=$user->getRoles();

        if(in_array('ROLE_SECTOR_MANAGER', $userRoles)) {
            $redirect  = $this->redirectToRoute('SMworkInterface');
        } else if(in_array('ROLE_PEEP', $userRoles)) {
            $redirect  = $this->redirectToRoute('peepInterface');
        }

        return $redirect;

//        return $this->redirectToRoute('SMworkInterface');
    }

    /**
     * @Route("/editcomment/{attendanceId}/{text}")
     */
    public function EditAttendanceComment($attendanceId, $text = null)
    {
        $this->denyAccessUnlessGranted('ROLE_ATTENDANCE_EDIT');
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
        $this->denyAccessUnlessGranted('ROLE_ATTENDANCE_FINE_EDIT');
        $entityManager = $this->getDoctrine()->getManager();

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendance = $attendanceRepo->find($attendanceId);
        $attendance->setFine($action);

        $entityManager->persist($attendance);
        $entityManager->flush();

        return $this->redirectToRoute('SMworkInterface');
    }

    /**
     * @Route("/approveFine/{fineId}", name="approveFine")
     */
    public function approveFine($fineId){
        $this->denyAccessUnlessGranted('ROLE_ATTENDANCE_FINE_APPROVE');

        $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendanceWitFine = $attendanceRepo->find($fineId);
        $attendanceWitFine->setFineApproved(true);

        $entityManager->persist($attendanceWitFine);
        $entityManager->flush();

        return $this->redirectToRoute('peepInterface');
    }


    /**
     * @Route("/declineFine/{fineId}", name="declineFine")
     */
    public function declineFine($fineId){
        $this->denyAccessUnlessGranted('ROLE_ATTENDANCE_FINE_APPROVE');

        $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendanceWitFine = $attendanceRepo->find($fineId);
        $attendanceWitFine->setFine(NULL);

        $entityManager->persist($attendanceWitFine);
        $entityManager->flush();

        return $this->redirectToRoute('peepInterface');
    }

    /**
     * @Route("/attendance/delete/{attendanceId}")
     */
    public function deleteAttendance($attendanceId)
    {
        $this->denyAccessUnlessGranted('ROLE_ATTENDANCE_DELETE');
        $entityManager = $this->getDoctrine()->getManager();

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendance = $attendanceRepo->find($attendanceId);
        $attendance->setFine("manually deleted");

        $entityManager->persist($attendance);
        $entityManager->flush();

        $user = $this->getUser();
        $userRoles=$user->getRoles();

        if(in_array('ROLE_SECTOR_MANAGER', $userRoles)) {
            $redirect  = $this->redirectToRoute('SMworkInterface');
        } else if(in_array('ROLE_PEEP', $userRoles)) {
            $redirect  = $this->redirectToRoute('peepInterface');
        }

        return $redirect;
        // return $this->redirectToRoute('SMworkInterface');
    }

    /**
     * @Route("/finishShift/", name="finishShift")
     */
    public function finishShift()
    {
        $this->denyAccessUnlessGranted('ROLE_ATTENDANCE_DELETE');
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);

        $attendancestoExit = $attendanceRepo->findActiveUsersOnSectorInShift($this->getUser()->getSector(),$this->getUser()->getShift());


        foreach ($attendancestoExit as $forceExit){
            $this->AddAttendance($forceExit->getLogin());
        }


        return $this->redirectToRoute('SMworkInterface');
    }

}
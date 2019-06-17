<?php


namespace App\Controller\Attendances;


use App\Entity\Attendance;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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

        $login = trim(strtolower($login));
        $attendance = new Attendance();
        $attendance->setLogin($login);
        $attendance->setDateTime(new \DateTime());
        $attendance->setShift($this->getUser()->getShift());
        $attendance->setSector($this->getUser()->getSector());

        if ($lastLoginAttendance && $lastLoginAttendance->getDirection() != 'exit') {
            $attendance->setDirection('exit');
            $attendance->setShift($lastLoginAttendance->getShift());

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

        return $this->redirectToRoute('peepInterface', [
            '_fragment'=>'finesTable',
        ]);
    }


    /**
     * @Route("/declineFine/{fineId}", name="declineFine")
     */
    public function declineFine($fineId){
        $this->denyAccessUnlessGranted('ROLE_ATTENDANCE_FINE_APPROVE');

        $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendanceWitFine = $attendanceRepo->find($fineId);
        $attendanceWitFine->setFineApproved(false);

        $entityManager->persist($attendanceWitFine);
        $entityManager->flush();

        return $this->redirectToRoute('peepInterface', [
            '_fragment'=>'finesTable',
        ]);
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


        $simulatedExit = clone $attendance;
        $simulatedExit->setDateTime(new \DateTime());
        $simulatedExit->setDirection('exit');
        $simulatedExit->setFine('manually deleted');
        $simulatedExit->setComment(NULL);
        $simulatedExit->setFineApproved(NULL);
        $entityManager->persist($simulatedExit);
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
        $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);

        $attendancestoExit = $attendanceRepo->findActiveUsersOnSector($this->getUser()->getSector());

        foreach ($attendancestoExit as $forceExit){
            $attendance = clone $forceExit;
            $attendance->setDateTime(new \DateTime());
            $attendance->setDirection('exit');
            $attendance->setFine(NULL);
            $attendance->setComment(NULL);
            $attendance->setFineApproved(NULL);
            $entityManager->persist($attendance);
        }

        $entityManager->flush();
        return $this->redirectToRoute('app_logout');
    }


    /**
     * @Route("/getUsersInSectorsQty", name="getUsersInSectorsQty")
     */
    public function getUsersInSectorsQty(){
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        //$attendances = $attendanceRepo->findUsersOnAllSectorsInShift($this->getUser()->getShift());

        $attendances = $attendanceRepo->findAllActiveWorkers();
        $attendancesOutput = [];
        $lastLogin = "";

        $usersInSectorQty = [];
        foreach (USER::SECTORS_LIST as $sector) {
            $usersInSectorQty[$sector]['total'] = 0;
            foreach (USER::PROVIDERS_LIST as $key => $value) {
                $usersInSectorQty[$sector][$key] = 0;
            }
            $usersInSectorQty[$sector]['lamoda'] = 0;
        }

        foreach ($attendances as $attendance) {
            if ($attendance->getLogin() != $lastLogin && $attendance->getDirection() == 'entrance') {
                $attendancesOutput[] = $attendance;
                if(isset($usersInSectorQty[$attendance->getSector()]['total'])){
                    $usersInSectorQty[$attendance->getSector()]['total']++;
                }
                if (substr($attendance->getLogin(), 2, 1) == '-') {
                    $providerPerfix = substr($attendance->getLogin(), 0, 2);
                    if(array_key_exists($providerPerfix, USER::PROVIDERS_LIST)) {
                        $usersInSectorQty[$attendance->getSector()][$providerPerfix]++;
                    }else {
                        if(isset($usersInSectorQty[$attendance->getSector()]['lamoda'])){
                            $usersInSectorQty[$attendance->getSector()]['lamoda']++;
                        }
                    }
                } else {
                    if(isset($usersInSectorQty[$attendance->getSector()]['lamoda'])){
                        $usersInSectorQty[$attendance->getSector()]['lamoda']++;
                    }
                }
            }
            $lastLogin = $attendance->getLogin();
        }

        $usersInSectorQty['SPECIAL_ARIAS']['total']=$usersInSectorQty['CAD_IN']['total']+$usersInSectorQty['QUALITY']['total'];
        $usersInSectorQty['OUTBOUND']['total']=
            $usersInSectorQty['PICK_M1']['total']+
            $usersInSectorQty['PICK_M2']['total']+
            $usersInSectorQty['PACK_MB']['total']+
            $usersInSectorQty['PACK_IS']['total']+
            $usersInSectorQty['SORT']['total']+
            $usersInSectorQty['LOAD']['total'];
        $usersInSectorQty['INBOUND']['total']=
            $usersInSectorQty['UNPACK']['total']+
            $usersInSectorQty['IN']['total']+
            $usersInSectorQty['RET']['total']+
            $usersInSectorQty['PUT_M1']['total']+
            $usersInSectorQty['PUT_M2']['total'];

        $usersInSectorQty['OPS_PERSONAL']['total']=$usersInSectorQty['OUTBOUND']['total']+$usersInSectorQty['INBOUND']['total'];
        $usersInSectorQty['NTT_PERSONAL']['total']=
            $usersInSectorQty['OPS_PERSONAL']['total']+
            $usersInSectorQty['PUP_porter']['total']+
            $usersInSectorQty['SPECIAL_ARIAS']['total']+
            $usersInSectorQty['INDITEX']['total']+
            $usersInSectorQty['JEWELRY']['total']+
            $usersInSectorQty['MAIN']['total'];

        foreach (USER::PROVIDERS_LIST as $key => $value) {
            $usersInSectorQty['SPECIAL_ARIAS'][$key]=$usersInSectorQty['CAD_IN'][$key]+$usersInSectorQty['QUALITY'][$key];

            $usersInSectorQty['OUTBOUND'][$key]=
                $usersInSectorQty['PICK_M1'][$key]+
                $usersInSectorQty['PICK_M2'][$key]+
                $usersInSectorQty['PACK_MB'][$key]+
                $usersInSectorQty['PACK_IS'][$key]+
                $usersInSectorQty['SORT'][$key]+
                $usersInSectorQty['LOAD'][$key];

            $usersInSectorQty['INBOUND'][$key]=
                $usersInSectorQty['UNPACK'][$key]+
                $usersInSectorQty['IN'][$key]+
                $usersInSectorQty['RET'][$key]+
                $usersInSectorQty['PUT_M1'][$key]+
                $usersInSectorQty['PUT_M2'][$key];

            $usersInSectorQty['OPS_PERSONAL'][$key]=
                $usersInSectorQty['OUTBOUND'][$key]+
                $usersInSectorQty['INBOUND'][$key];

            $usersInSectorQty['NTT_PERSONAL'][$key]=
                $usersInSectorQty['OPS_PERSONAL'][$key]+
                $usersInSectorQty['PUP_porter'][$key]+
                $usersInSectorQty['SPECIAL_ARIAS'][$key]+
                $usersInSectorQty['INDITEX'][$key]+
                $usersInSectorQty['JEWELRY'][$key]+
                $usersInSectorQty['MAIN'][$key];
        }


        return new JsonResponse($usersInSectorQty);
    }



}
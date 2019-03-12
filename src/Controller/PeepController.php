<?php


namespace App\Controller;


use App\Entity\Attendance;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PeepController extends AbstractController
{

    /**
     * @Route("/peepInterface", name="peepInterface")
     */
    public function PeepInterface()
    {
        $this->denyAccessUnlessGranted('ROLE_PEEP');

        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendances = $attendanceRepo->findUsersOnAllSectorsInShift($this->getUser()->getShift());

        //dd($attendances);
        $attendancesOutput=[];
        $lastLogin ="";
        $usersInSectorQty=[];
        foreach (USER::SECTORS_LIST as $sector) {
            $usersInSectorQty[$sector]['total']=0;
            foreach (USER::PROVIDERS_LIST as $key=>$value){
                $usersInSectorQty[$sector][$key]=0;
            }
            $usersInSectorQty[$sector]['lamoda']=0;
        }

//        dd($usersInSectorQty);
        //dump($attendances);
        foreach ($attendances as $attendance ) {
            if ($attendance->getLogin()!=$lastLogin && $attendance->getDirection()=='entrance'){
                $attendancesOutput[]=$attendance;
                $usersInSectorQty[$attendance->getSector()]['total']++;
                if(substr($attendance->getLogin(),2,1)=='_'){
                    $providerPerfix= substr($attendance->getLogin(),0,2);
                    $usersInSectorQty[$attendance->getSector()][$providerPerfix]++;
                } else {
                    $usersInSectorQty[$attendance->getSector()]['lamoda']++;
                }
            }
            $lastLogin=$attendance->getLogin();
        }

        //dump($attendancesOutput);
        $attendancesWithFinesWithoutApproval=[];
        $attendancesWithFinesWithoutApproval=$attendanceRepo->findAllAttendancesWithFinesWithoutApproval();


        //dd($attendances);
        return $this->render('Peep/PeepInterface.html.twig', [
            'sectors'=>USER::SECTORS_LIST,
            'attendances'=>$attendancesOutput,
            'usersInSectors'=>$usersInSectorQty,
            'attendancesForApproval'=>$attendancesWithFinesWithoutApproval,
            ]);
    }


    /**
     * @Route("/approveFine/{fineId}", name="approveFine")
     */
    public function approveFine($fineId){
        $this->denyAccessUnlessGranted('ROLE_PEEP');

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
        $this->denyAccessUnlessGranted('ROLE_PEEP');

        $entityManager = $this->getDoctrine()->getManager();
        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendanceWitFine = $attendanceRepo->find($fineId);
        $attendanceWitFine->setFine(NULL);

        $entityManager->persist($attendanceWitFine);
        $entityManager->flush();

        return $this->redirectToRoute('peepInterface');
    }


    /**
     * @Route("/setShift/{shiftId}", name="setShift")
     */
    public function setShift($shiftId){
        $this->denyAccessUnlessGranted('ROLE_PEEP');

        $entityManager = $this->getDoctrine()->getManager();
        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        $userForSetShift = $this->getUser();

        $userForSetShift->setShift($shiftId);

        $entityManager->persist($userForSetShift);
        $entityManager->flush();

        return $this->redirectToRoute('peepInterface');
    }

    /**
     * @Route("/peepAttendance/new/{login}", name ="AddAttendanceByPeep")
     */
    public function AddAttendanceByPeep($login)
    {
        $this->denyAccessUnlessGranted('ROLE_PEEP');


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
        return $this->redirectToRoute('peepInterface');
    }


}
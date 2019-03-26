<?php


namespace App\Controller;


use App\Controller\Users\Forms\AddUserForm;
use App\Entity\Attendance;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{

    /**
     * @Route("/userlist", name="userlist")
     */
    public function userlistShow(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        //$users = $usersRepo->findAll();
        $users = $usersRepo->findAllAdminsButOnlyMyShiftSectorManagets($this->getUSer()->getShift());

        //$NewUser = new User();
        $AddUserForm = $this->createForm(AddUserForm::class, new User());

        $AddUserForm->handleRequest($request);
        if ($AddUserForm->isSubmitted() && $AddUserForm->isValid()) {
            $NewUser = $AddUserForm->getData();
            $plainPassword = $NewUser->getPassword();

            $encoded = $encoder->encodePassword($NewUser, $plainPassword);
            $NewUser->setPassword($encoded);
            $em->persist($NewUser);
            $em->flush();
            return $this->redirectToRoute('userlist');
        }


        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
//        $attendances = $attendanceRepo->findUsersOnAllSectorsInAllShifts();
        $attendances = $attendanceRepo->findUsersOnAllSectorsInShift($this->getUser()->getShift());
        //dd($attendances);
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
                    if(isset($usersInSectorQty[$attendance->getSector()][$providerPerfix])){
                        $usersInSectorQty[$attendance->getSector()][$providerPerfix]++;
                    }

                } else {
                    if(isset($usersInSectorQty[$attendance->getSector()]['lamoda'])){
                        $usersInSectorQty[$attendance->getSector()]['lamoda']++;
                    }
                }
            }
            $lastLogin = $attendance->getLogin();
        }


        //dd($sectors);
        return $this->render('Admin/Userlist.html.twig', [
            'users' => $users,
            'addUserForm' => $AddUserForm->createView(),
            'sectors' => USER::SECTORS_LIST,
            'attendances' => $attendancesOutput,
            'usersInSectors' => $usersInSectorQty,

        ]);
    }

}
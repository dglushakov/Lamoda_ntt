<?php


namespace App\Controller;


use App\Controller\Users\AddUserForm;
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
        $users = $usersRepo->findAll();

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
        $attendances = $attendanceRepo->findUsersOnAllSectorsInAllShifts();

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
                $usersInSectorQty[$attendance->getSector()]['total']++;
                if (substr($attendance->getLogin(), 2, 1) == '_') {
                    $providerPerfix = substr($attendance->getLogin(), 0, 2);
                    $usersInSectorQty[$attendance->getSector()][$providerPerfix]++;
                } else {
                    $usersInSectorQty[$attendance->getSector()]['lamoda']++;
                }
            }
            $lastLogin = $attendance->getLogin();
        }


        //dd($sectors);
        return $this->render('Admin/Userlist.html.twig', [
            'users' => $users,
            'addUserFOrm' => $AddUserForm->createView(),
            'sectors' => USER::SECTORS_LIST,
            'attendances' => $attendancesOutput,
            'usersInSectors' => $usersInSectorQty,

        ]);
    }


    /**
     * @Route ("userlist/delete/{id}", name="deleteUser")
     */
    public function deleteUser(EntityManagerInterface $em, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        $userToDelete = $usersRepo->find($id);

        if ($userToDelete) {
            $em->remove($userToDelete);
            $em->flush();
        }
        return $this->redirectToRoute('userlist');
    }


}
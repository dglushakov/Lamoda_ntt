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
    public function userlistShow(EntityManagerInterface $em,Request $request, UserPasswordEncoderInterface $encoder)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        $users = $usersRepo->findAll();

        $NewUser = new User();
        $AddUserForm = $this->createForm(AddUserForm::class, $NewUser);

        $AddUserForm->handleRequest($request);
        if ($AddUserForm->isSubmitted() && $AddUserForm->isValid()) {
            $NewUser=$AddUserForm->getData();
            $plainPassword =$NewUser->getPassword();

            $encoded = $encoder->encodePassword($NewUser, $plainPassword);
            $NewUser->setPassword($encoded);
            $em->persist($NewUser);
            $em->flush();
            return $this->redirectToRoute('userlist');
        }


        $attendanceRepo = $this->getDoctrine()->getRepository(Attendance::class);
        $attendances = $attendanceRepo->findUsersOnAllSectorsInShift($this->getUser()->getShift());


        return $this->render('Admin/Userlist.html.twig',[
            'users'=>$users,
            'addUserFOrm'=>$AddUserForm->createView(),
            'sectors'=>USER::SECTORS_LIST,
            'attendances'=>$attendances,
        ]);
    }


    /**
     * @Route ("userlist/delete/{id}", name="deleteUser")
     */
    public function deleteUser(EntityManagerInterface $em, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_PEEP');
        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        $userToDelete = $usersRepo->find($id);

        if ($userToDelete){
            $em->remove($userToDelete);
            $em->flush();
        }
        return $this->redirectToRoute('userlist');
    }


}
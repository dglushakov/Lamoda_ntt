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

        $users = $usersRepo->findAllAdminsButOnlyMyShiftSectorManagets($this->getUSer()->getShift());
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


        return $this->render('Admin/Userlist.html.twig', [
            'users' => $users,
            'addUserForm' => $AddUserForm->createView(),
            'sectors' => USER::SECTORS_LIST,
        ]);
    }

}
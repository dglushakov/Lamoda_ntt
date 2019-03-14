<?php


namespace App\Controller\Users;


use App\Controller\Users\Forms\EditUserForm;
use App\Controller\Users\Forms\EditUserPasswordForm;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController
{
    /**
     * @Route ("userlist/delete/{id}", name="deleteUser")
     */
    public function deleteUser(EntityManagerInterface $em, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USERS_DELETE');
        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        $userToDelete = $usersRepo->find($id);

        if ($userToDelete) {
            $em->remove($userToDelete);
            $em->flush();
        }
        return $this->redirectToRoute('userlist');
    }

    /**
     * @Route ("userlist/edit/{id}", name="editUser")
     */
    public function editUser(EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USERS_EDIT');
        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        $userToEdit = $usersRepo->find($id);

        $EditUserForm = $this->createForm(EditUserForm::class, $userToEdit);

        $EditUserForm->handleRequest($request);
        if ($EditUserForm->isSubmitted() && $EditUserForm->isValid()) {
            $userToEdit = $EditUserForm->getData();

            $em->persist($userToEdit);
            $em->flush();
            return $this->redirectToRoute('userlist');
        }

        $EditUserPasswordForm = $this->createForm(EditUserPasswordForm::class, $userToEdit);
        $EditUserPasswordForm->handleRequest($request);
        if ($EditUserPasswordForm->isSubmitted() && $EditUserPasswordForm->isValid()) {
            $userToEdit = $EditUserForm->getData();

            $plainPassword = $EditUserForm->getData()->getPassword();

            $encoded = $encoder->encodePassword($userToEdit, $plainPassword);
            $userToEdit->setPassword($encoded);
            $em->persist($userToEdit);
            $em->flush();
            return $this->redirectToRoute('editUser', [
                'id'=>$userToEdit->getId(),
            ]);
        }


        return $this->render('Admin/editUser.html.twig',[
            'editUserForm'=>$EditUserForm->createView(),
            'editUserPasswordForm'=>$EditUserPasswordForm->createView(),
        ]);
    }

    /**
     * @Route("/setShift/{shiftId}", name="setShift")
     */
    public function setShift($shiftId){
        $this->denyAccessUnlessGranted('ROLE_USER_SHIFT_CHANGE');

        $entityManager = $this->getDoctrine()->getManager();
        $usersRepo = $this->getDoctrine()->getRepository(User::class);
        $userForSetShift = $this->getUser();

        $userForSetShift->setShift($shiftId);

        $entityManager->persist($userForSetShift);
        $entityManager->flush();

        return $this->redirectToRoute('peepInterface');
    }
}
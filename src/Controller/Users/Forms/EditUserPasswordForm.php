<?php


namespace App\Controller\Users\Forms;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Security;

class EditUserPasswordForm extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('UserName', TextType::class,[
                'required'=>true,
                'label'=>'Пользователь'

            ])
            ->add('password', PasswordType::class,[
                'required'=>true,
                'label'=> 'Пароль',
            ])
        ;

    }
}
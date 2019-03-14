<?php


namespace App\Controller\Users\Forms;


use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Security;

class AddUserForm extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $userRoles=[
            'Sector Manager' => 'ROLE_SECTOR_MANAGER',
            'Peep' => 'ROLE_PEEP',
            'Admin'=> 'ROLE_ADMIN',
        ];

//        $currentUserRoles = $this->security->getUser()->getRoles();
//        if(in_array('ROLE_GOD', $currentUserRoles)){
//            $userRoles['Admin']='ROLE_ADMIN';
//        }

        $builder
            ->add('UserName', TextType::class,[
                'required'=>true,
                'label'=>'Пользователь'

            ])
            ->add('password', PasswordType::class,[
                'required'=>true,
                'label'=> 'Пароль',
                ])
            ->add('roles', ChoiceType::class,[
                    'choices' => $userRoles,
                    'expanded' => true,
                    'multiple' => true,
                    'label'=> 'Роль',
            ])
            ->add('sector', ChoiceType::class,[
                'required'=>true,
                'choices'=>USER::SECTORS_LIST,
                'label'=> 'Участок',
            ])
            ->add('shift', ChoiceType::class,[
                'required'=>true,
                'choices'=>USER::NUMBERS_OF_SHIFTS,
                'label'=> 'Смена',
                'placeholder'=>'Выберите смену'
            ] )
        ;

    }

}
<?php


namespace App\Controller\Users;


use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddUserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('UserName', TextType::class,[
                'required'=>true,
                'label'=>'Пользователь'

            ])
//            ->add('roles'
//            )
            ->add('password', PasswordType::class,[
                'required'=>true,
                'label'=> 'Пароль',
                ])
            ->add(
                'roles', ChoiceType::class, [
                    'choices' => ['Админ' => 'ROLE_ADMIN', 'ROLE_CUSTOMER' => 'ROLE_CUSTOMER'],
                    'expanded' => false,
                    'multiple' => true,
                    'label'=> 'Роль',
                ]
            )
            ->add('sector', ChoiceType::class,[
                'required'=>true,
                'choices'=>USER::SECTORS_LIST,
                'label'=> 'Участок',
            ])
            ->add('shift', ChoiceType::class,[
                'required'=>true,
                'choices'=>USER::NUMBERS_OF_SHIFTS,
                'label'=> 'Смена',
            ] )
        ;




        //        parent::buildForm($builder, $options); // TODO: Change the autogenerated stub
    }

}
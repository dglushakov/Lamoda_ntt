<?php


namespace App\Controller\Reports\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\User;

class FiltersForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $depthFilter = [];
        for ($i=1; $i<46; $i++) {
            $depthFilter[$i]=$i;
        }
        $builder
            ->add('Provider', ChoiceType::class, [
                'choices' => USER::PROVIDERS_LIST,
                'required' => false,

            ])
            ->add('Sector', ChoiceType::class, [
                'choices' => USER::SECTORS_LIST,
                'required' => false,
            ])
            ->add('dateFrom', DateType::class, [
                'widget' => 'choice',
                'required' => false,
                'data'=> new \DateTime('-3 days'),
            ])

            ->add('dateTo', DateType::class, [
                'required' => false,
                'data'=> new \DateTime(),
            ])

//            ->add('dateFrom', DateType::class, [
//                'widget' => 'choice',
//            ])
//            ->add('dateTo', DateType::class, [
//                'widget' => 'choice',
//            ])

        ;
    }

}
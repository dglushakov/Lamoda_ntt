<?php


namespace App\Controller\Reports\Form;


use App\Entity\Attendance;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class finesReportFiltersForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $depthFilter = [];
        for ($i=1; $i<46; $i++) {
            $depthFilter[$i]=$i;
        }
        $builder
            ->add('Fine', ChoiceType::class, [
                'choices' => Attendance::FINES,
                'required' => false,

            ])
            ->add('Sector', ChoiceType::class, [
                'choices' => USER::SECTORS_LIST,
                'required' => false,
            ])
            ->add('Depth', ChoiceType::class, [
                'choices' => $depthFilter,
                'required' => false,
            ])
        ;
    }
}
<?php

namespace App\Form;

use App\Entity\Formateur;
use App\Entity\Formation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('designation')
            ->add('description')
            ->add('startDate')
            ->add('endDate')
            ->add('state')
            ->add('students')
            ->add('formateur', EntityType::class, array(
                'choice_label' => function(Formateur $formateur) {
                    return (sprintf("%s-%s", $formateur->getName(),$formateur->getField() ));
                }
            ))
            ->add('topics')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}

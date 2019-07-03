<?php

namespace App\Form;

use App\Entity\Formateur;
use App\Entity\Formation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('designation')
            ->add('description')
            ->add('image')
            ->add('startDate', null, array(
                'widget' => 'single_text'
            ))
            ->add('endDate', null, array(
                'widget' => 'single_text'
            ))
            ->add('state')
            ->add('students')
            ->add('formateur', null, array(
                'choice_label' => function(Formateur $formateur) {
                    return (sprintf("%s-%s", $formateur->getName(),$formateur->getField() ));
                }
            ))
            ->add('topics')
            ->add('ajouter',SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn btn-primary'
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}

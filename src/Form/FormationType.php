<?php

namespace App\Form;

use App\Entity\Formateur;
use App\Entity\Formation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tabConstraints = array(
          new Image(),
        );
        $isUpdate = $options['data']->getId() ?? 0 ;

        if (!$isUpdate) {
            $tabConstraints[] = new NotNull(
                array(
                    'message'=>'Please upload your image'
                )
            );
        }
        $builder
            ->add('designation', TextType::class)
            ->add('description', TextareaType::class)
            ->add('imageFile', FileType::class, array(
                'mapped'=> false,
                'constraints'=> $tabConstraints
            ))
            ->add('startDate', DateTimeType::class, array(
                'widget' => 'single_text'
            ))
            ->add('endDate', DateTimeType::class, array(
                'widget' => 'single_text'
            ))
            ->add('state')
            ->add('students')
            ->add('formateur', EntityType::class, array(
                'class' => Formateur::class,
                'choice_label' => function(Formateur $formateur) {
                    return (sprintf("%s-%s", $formateur->getName(),$formateur->getField() ));
                }
            ))
            ->add('topics')
            ->add('editer',SubmitType::class, array(
                'attr' => array(
                    'class' => 'btn btn-primary'
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Formation::class
        ]);
    }
}

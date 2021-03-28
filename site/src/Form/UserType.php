<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // le champ isAdmin est rempli par défaut à false et on ne veut pas qu'un
        // utilisateur puisse le devenir
        $builder
            ->add('login',
                TextType::class,
                ['label' => 'identifiant'])
            ->add('password',
                PasswordType::class,
                ['label' => 'mot de passe'])
            ->add('name',
                TextType::class,
                ['label' => 'nom'])
            ->add('firstname',
                TextType::class,
                ['label' => 'prénom'])
            ->add('birthdate',
                DateType::class,
                ['label' => 'date de naissance'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

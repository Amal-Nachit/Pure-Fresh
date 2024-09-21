<?php

namespace App\Form;

use App\Entity\PureUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PureUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Entrez votre email'
                ],
                'label' => 'Email',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Entrez un mot de passe'
                ],
                'label' => 'Mot de passe',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Entrez votre prénom'
                ],
                'label' => 'Prénom',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Entrez votre nom'
                ],
                'label' => 'Nom',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('telephone', TelType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Entrez votre numéro de téléphone'
                ],
                'label' => 'Téléphone',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('adresse', TextType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Entrez votre adresse'
                ],
                'label' => 'Adresse',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])      ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PureUser::class,
        ]);
    }
}

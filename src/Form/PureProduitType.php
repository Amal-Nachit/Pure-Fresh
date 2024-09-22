<?php

namespace App\Form;

use App\Entity\PureCategorie;
use App\Entity\PureProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PureProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Entrez le nom du produit'
                ],
                'label' => 'Nom du produit',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-textarea bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Décrivez le produit'
                ],
                'label' => 'Description',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-file bg-gray-700 text-white border-gray-600 rounded-lg p-2 mb-6',
                ],
                'label' => 'Image',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('prix', NumberType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Entrez le prix du produit'
                ],
                'label' => 'Prix',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => PureCategorie::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'form-select bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                ],
                'label' => 'Catégorie',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PureProduit::class,
        ]);
    }
}

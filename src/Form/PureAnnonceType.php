<?php
namespace App\Form;

use App\Entity\PureAnnonce;
use App\Entity\PureProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PureAnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Entrez le titre de l\'annonce'
                ],
                'label' => 'Titre',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('quantite', IntegerType::class, [
                'attr' => [
                    'class' => 'form-input bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Quantité en kg'
                ],
                'label' => 'Quantité (kg)',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-textarea bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                    'placeholder' => 'Décrivez votre annonce'
                ],
                'label' => 'Description de l\'annonce',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
            ->add('pureProduit', EntityType::class, [
                'class' => PureProduit::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'form-select bg-gray-700 text-white border-gray-600 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-6',
                ],
                'label' => 'Produit',
                'label_attr' => [
                    'class' => 'block text-yellow-400 font-semibold mb-2'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PureAnnonce::class,
        ]);
    }
}

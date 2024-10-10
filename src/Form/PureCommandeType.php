<?php

namespace App\Form;

use App\Entity\PureCommande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PureCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
         
    $builder
    ->add('quantite', IntegerType::class, [
        'label' => 'Quantité (en kg) :',
        'attr' => [
            'class' => 'mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm',
            'min' => 1,
            'placeholder' => 'Entrez la quantité',
        ],
    ])
            ->add('total', TextType::class, [
                'label' => 'Prix total',
                'attr' => ['readonly' => true],
            ]);
    ;}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PureCommande::class,
        ]);
    }
}

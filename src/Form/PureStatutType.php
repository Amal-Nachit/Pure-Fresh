<?php

namespace App\Form;

use App\Entity\PureStatut;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PureStatutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('statut', ChoiceType::class, [
            'choices' => array_flip(array_map(fn($statut) => $statut->getIntitule(), $options['statuts'])),
            'label' => 'Changer le statut',
        ]);  }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PureStatut::class,
            'statuts' => [],
        ]);
    }
}

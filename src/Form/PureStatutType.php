<?php

namespace App\Form;

use App\Entity\PureCommande;
use App\Entity\PureStatut;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PureStatutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('statut', EntityType::class, [
            'class' => PureStatut::class, // L'entité à utiliser
            'choice_label' => 'intitule', // Champ à afficher dans le select
            'label' => 'Changer le statut',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PureCommande::class,
            'statuts' => [],
        ]);
    }
}

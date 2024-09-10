<?php

namespace App\Form;

use App\Entity\PureAnnonce;
use App\Entity\PureProduit;
use App\Entity\PureUser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PureAnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('quantite')
            ->add('description')
            ->add('disponibilite')
            ->add('datePublication', null, [
                'widget' => 'single_text',
            ])
            ->add('dateExpiration', null, [
                'widget' => 'single_text',
            ])
            ->add('approuve')
            ->add('pureProduit', EntityType::class, [
                'class' => PureProduit::class,
                'choice_label' => 'id',
            ])
            ->add('pureUser', EntityType::class, [
                'class' => PureUser::class,
                'choice_label' => 'id',
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

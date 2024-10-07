<?php

namespace App\Form;

use App\Entity\PureAnnonce;
use App\Entity\PureCommande;
use App\Entity\PureStatut;
use App\Entity\PureUser;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PureCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite') 
            ->add('total', null, [
                'attr' => ['readonly' => true] 
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PureCommande::class,
        ]);
    }
}

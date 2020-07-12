<?php

namespace App\Form;

use App\Entity\DirecteurPedagogique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DirecteurPedagogiqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo')
            ->add('nom')
            ->add('prenom')
            ->add('password')
            ->add('addresse')
            ->add('email')
            ->add('telephone')
            ->add('deleted')
            ->add('dateNaissance')
            ->add('photoUrl')
            ->add('etablissement')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DirecteurPedagogique::class,
        ]);
    }
}

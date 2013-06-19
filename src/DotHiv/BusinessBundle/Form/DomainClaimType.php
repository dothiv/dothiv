<?php

namespace DotHiv\BusinessBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DomainClaimType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('claimingToken')
            ->add('domain', 'text', array('read_only' => true))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'DotHiv\BusinessBundle\Entity\DomainClaim',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }
}
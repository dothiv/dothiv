<?php

namespace Dothiv\BusinessBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id')
            ->add('redirect_domain')
            ->add('language')
            ->add('position')
            ->add('position_alternative')
            ->add('domain', 'entity_id', array('class' => 'Dothiv\BusinessBundle\Entity\Domain'))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dothiv\BusinessBundle\Entity\Banner',
            'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }
}

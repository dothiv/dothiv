<?php

namespace Dothiv\BusinessBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Form type used for user editing.
 *
 * @author Benedikt Budig <bb@dothiv.org>
 *
 */
class UserEditType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('email');
        $builder->add('name');
        $builder->add('surname');
        $builder->add('username', 'text', array('read_only' => true));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Dothiv\BusinessBundle\Entity\User',
                'csrf_protection' => false
        ));
    }

    public function getName()
    {
        return '';
    }

}

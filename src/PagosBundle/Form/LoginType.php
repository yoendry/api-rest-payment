<?php

namespace PagosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class LoginType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('_username',TextType::class,array(
        		'label' => 'Nombre: ',
        		'attr' => array('autocomplete' => 'off')
        ))
        ->add('_password', PasswordType::class,array(
        		'label' => 'Contraseña: ',
        		'attr' => array('autocomplete' => 'off')
        ));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
    	$resolver->setDefaults(array(
    			'data_class' => null,
    			'csrf_protection' => true,
    			'csrf_field_name' => '_token',
            	'csrf_token_id'   => 'task_item',
    	));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return null;
    }


}

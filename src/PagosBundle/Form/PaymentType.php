<?php

namespace PagosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use PagosBundle\Entity\Status;
use PagosBundle\Entity\PaymentMethod;
use PagosBundle\Entity\Company;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Length;
use PagosBundle\Entity\Payment;

class PaymentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('paymentDate', DateType::class,array(
        		'widget' => 'single_text',
        		'label' => 'Fecha de pago: ',
        		'format' => "dd/mm/yyyy"
        ))
        ->add('amount', null, array(
        		'label' => 'Monto: ',
        		'attr' => array('autocomplete' => 'off')
        ))
        ->add('externalReference', TextType::class,array(
        		'label' => 'Referencia Externa',
        		'attr' => array('autocomplete' => 'off')
        ))
        ->add('terminal', NumberType::class,array(
       			'constraints' => new Length(array('max' => 4)),
        		'label' => 'Terminal: ',
        		'attr' => array('autocomplete' => 'off')
        ))
        ->add('reference')
        ->add('company',EntityType::class,array(
				'class' => Company::class,
        		'label' => 'Compañía: ',
        		'attr' => array('autocomplete' => 'off')
		))
        ->add('paymentMethod', EntityType::class,array(
				'class' => PaymentMethod::class,
        		'label' => 'Método de Pago: ',
        		'attr' => array('autocomplete' => 'off')
		))
        ->add('status',EntityType::class,array(
				'class' => Status::class,
        		'label' => 'Estado: ',
        		'attr' => array('autocomplete' => 'off')
		));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Payment::class
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

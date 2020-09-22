<?php

namespace PagosBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\Length;

use PagosBundle\Entity\Payment;

use Doctrine\Common\Persistence\ObjectManager;
use PagosBundle\Form\DataTransformer\CompanyToStringTransformer;
use PagosBundle\Form\DataTransformer\PaymentMethodToStringTransformer;
use PagosBundle\Form\DataTransformer\StatusToStringTransformer;

class PaymentType extends AbstractType
{
    private $manager;
    
      public function __construct(ObjectManager $manager )   {
        $this->manager = $manager;
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('payment_date', DateType::class, array(
            'widget' => 'single_text',
            'format' => "yyyy-mm-dd'T'HH:mm:ssZZZ",
            'property_path' => 'paymentDate'
        ))
        ->add('amount', null, array(
        		'label' => 'Monto: ',
        		'attr' => array('autocomplete' => 'off')
        ))
        ->add('external_reference', TextType::class,array(
            'property_path' => 'externalReference'
        ))
        ->add('terminal', NumberType::class,array())
        ->add('reference')
        ->add('company',TextType::class,array())
		->add('payment_method', TextType::class,array(
    		    'property_path' => 'paymentMethod'
		))
		->add('status',TextType::class,array());
		
		
		$builder->get('company')->addModelTransformer(new CompanyToStringTransformer($this->manager));
		$builder->get('payment_method')->addModelTransformer(new PaymentMethodToStringTransformer($this->manager));
		$builder->get('status')->addModelTransformer(new StatusToStringTransformer($this->manager));
		
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Payment::class,
            'allow_extra_fields' => true,
            'csrf_protection' => false,
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

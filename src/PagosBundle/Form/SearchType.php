<?php

namespace PagosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PagosBundle\Entity\Company;
use PagosBundle\Entity\PaymentMethod;
use Lexik\Bundle\FormFilterBundle\Filter\Doctrine\ORMQuery;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderExecuterInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('company',Filters\EntityFilterType::class,array(
        		'class' => Company::class,
        		'label' => 'Empresa: ',
        		'attr' => array('autocomplete' => 'off')
        ))
        ->add('paymentMethod', Filters\EntityFilterType::class,array(
				'class' => PaymentMethod::class,
        		'label' => 'Método de Pago: ',
        		'attr' => array('autocomplete' => 'off')
		));
                
		$builder->add('date_desde', Filters\TextFilterType::class, array(
				'label' => 'Fecha desde: ',
				'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
				if (empty($values['value'])) {
					return null;
				}
		
				$mainAlias = current($filterQuery->getQueryBuilder()->getDQLPart('from'))->getAlias();
				$paramName = sprintf('p_%s', str_replace('.', '_', $field));		
		
				$expression = $filterQuery->getExpr()->gte($mainAlias . '.paymentDate', ':' . $paramName);
		
				$parameters = array($paramName => $values['value']);
		
				return $filterQuery->createCondition($expression, $parameters);
				},
				));
		$builder->add('date_hasta', Filters\TextFilterType::class, array(
				'label' => 'Fecha hasta: ',
				'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
				if (empty($values['value'])) {
					return null;
				}
		
				$mainAlias = current($filterQuery->getQueryBuilder()->getDQLPart('from'))->getAlias();
				$paramName = sprintf('p_%s', str_replace('.', '_', $field));		
		
				$expression = $filterQuery->getExpr()->lte($mainAlias . '.paymentDate', ':' . $paramName);
		
				$parameters = array($paramName => $values['value']);
		
				return $filterQuery->createCondition($expression, $parameters);
				},
				));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
    	$resolver->setDefaults(array(
    			'data_class' => null,
    			'csrf_protection'   => false,
        		'method'            => 'get'
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

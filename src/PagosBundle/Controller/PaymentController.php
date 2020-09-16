<?php

namespace PagosBundle\Controller;

use PagosBundle\Entity\Payment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use PagosBundle\Form\StatusType;
use PagosBundle\Form\SearchType;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Payment controller.
 *
 * @Route("/")
 */
class PaymentController extends Controller
{
    /**
     * Lists all payment entities.
     *
     * @Route("/", name="payment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $payments = $em->getRepository('PagosBundle:Payment')->findAll();

        $items = array();
        	
        foreach ($payments as $payment) {
        	array_push($items, $payment->getData());
        }
        
        return $this->render('payment/index.html.twig', array(
            'payments' => $items,
        ));
    }

    /**
     * Creates a new payment entity.
     *
     * @Route("/payments", name="payment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    	try {
    		$payment = new Payment();
    		$form = $this->createForm('PagosBundle\Form\PaymentType', $payment);
    		$form->handleRequest($request);
    		
    		if ($form->isSubmitted() && $form->isValid()) {
    			$em = $this->getDoctrine()->getManager();
    			$em->persist($payment);
    			$em->flush();
    		
    			return $this->redirectToRoute('payment_show', array('id' => $payment->getId()));
    		}
    		
    		return $this->render('payment/new.html.twig', array(
    				'payment' => $payment,
    				'form' => $form->createView(),
    		));
    	} catch (\Exception $e) {

    		$data = $request->get('pagosbundle_payment');
    		if (strpos($e->getMessage(), 'Integrity constraint violation')) {
    			$error = "Ya existe un pago con los siguientes datos únicos external_reference => ".$data['externalReference']." y payment_method =>  ".$data['paymentMethod'];
    		}else {
    			$error = 'Estamos teniendo problemas. Por favor, inténtelo de nuevo más tarde';
    		}
    		
			throw new \Exception($error,404);
    	}
        
    }
    
    /**
     * Update Status
     * @Route("/payments/{id}/", name="payment_update_status")
     * @Method({"GET", "POST"})
     */
    public function updateStatusAction(Request $request, Payment $payment)
    {
    	$editForm = $this->createForm('PagosBundle\Form\StatusType', $payment);
    	$editForm->handleRequest($request);
    	
    	if ($editForm->isSubmitted() && !$editForm->isEmpty()) {
    		$this->getDoctrine()->getManager()->flush();
    
    		return $this->redirectToRoute('payment_index');
    	}
    
    	return $this->render('payment/update_status.html.twig', array(
    			'payment' => $payment,
    			'edit_form' => $editForm->createView()
    	));
    }    

    /**
     * Finds and displays a payment entity.
     *
     * @Route("/{id}", name="payment_show")
     * @Method("GET")
     */
    public function showAction(Payment $payment)
    {
        return $this->render('payment/show.html.twig', array(
            'payment' => $payment
        ));
    }

    /**
     * Displays a form to edit an existing payment entity.
     *
     * @Route("/{id}/edit", name="payment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Payment $payment)
    {
        $editForm = $this->createForm('PagosBundle\Form\PaymentType', $payment);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('payment_edit', array('id' => $payment->getId()));
        }

        return $this->render('payment/edit.html.twig', array(
            'payment' => $payment,
            'edit_form' => $editForm->createView()
        ));
    }
    
    /**
     * Search Advance
     *
     * @Route("/search_advance/", name="payment_search_advance")
     * @Method("GET")
     */
    public function search_advanceAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$filterForm = $this->get('form.factory')->create(SearchType::class);
    	$filterBuilder = $em->getRepository('PagosBundle:Payment')->createQueryBuilder('p');
    	$filterForm->handleRequest($request);   		
    	$items = array();
    	
    	if ($filterForm->isSubmitted() && $filterForm->isValid()) {    		
    		$filterBuilder = $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $filterBuilder);
    		$payments =$filterBuilder->getQuery()->getResult();
    		foreach ($payments as $payment) {
    			array_push($items, $payment->getData());
    		}    		
    	}
    	
    	return $this->render('payment/search.html.twig', array(
    			'search_form' => $filterForm->createView(),
    			'payments' => $items
    	));
    }    

    /**
     * Deletes a payment entity.
     *
     * @Route("/delete/{id}", name="payment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $payment = $em->getRepository('PagosBundle:Payment')->find($request->get('id'));
        
        $em->remove($payment);
        $em->flush();
        
        return $this->redirectToRoute('payment_index');
    }    
}

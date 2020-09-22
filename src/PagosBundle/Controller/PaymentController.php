<?php

namespace PagosBundle\Controller;

use PagosBundle\Entity\Payment;
use PagosBundle\Form\PaymentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
    		$form = $this->createForm(PaymentType::class, $payment);
    		$form->handleRequest($request);
    		
    		if ($request->isMethod('POST')) {
    		        $data = json_decode($request->getContent(),true);
    		        
    		        $form->submit($data);   
    		        
    		        if ($form->isValid()) {    		            
    		            $em = $this->getDoctrine()->getManager();
    		            $em->persist($payment);
    		            $em->flush();
    		            
    		            return json_encode($payment,true);
    		        }    		        
    		}    		
    		
    		return $this->render('payment/new.html.twig', array(
    				'payment' => $payment,
    				'form' => $form->createView(),
    		));
    	} catch (\Exception $e) {
    	    dump($e->getMessage());exit;    	
    		
            throw new \Exception($e->getMessage(),404);
    	}
        
    }
    
   
}

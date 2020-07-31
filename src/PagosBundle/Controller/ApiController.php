<?php

namespace PagosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use PagosBundle\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;
use PagosBundle\Entity\Status;
use PagosBundle\Entity\PaymentMethod;
use PagosBundle\Entity\Company;

class ApiController extends Controller
{
	
	/**
	 * Create Pyment
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function createpaymentAction(Request $request)
	{
	
		$em = $this->getDoctrine()->getManager();
		 
		$status = new Status();
		$payment = new Payment();
		$mensaje="";
		 
		$data = json_decode($request->getContent());
		 
		$rep_status = $em->getRepository("PagosBundle:Status");
		$status_data = $rep_status->findOneBy(array('name' => $data->status));
		 
		$rep_payment_method = $em->getRepository("PagosBundle:PaymentMethod");
		$payment_method_data = $rep_payment_method->findOneBy(array('name' => $data->payment_method));
		 
		$rep_company = $em->getRepository("PagosBundle:Company");
		$company_data = $rep_company->findOneBy(array('name' => $data->company));
		 
		if(empty($status_data)){
			$mensaje .= "Error validating fields: status does not exist.";
		} elseif (empty($payment_method_data)){
			$mensaje .= "Error validating fields: payment_method does not exist.";
		} elseif (empty($company_data)){
			$mensaje .= "Error validating fields: company does not exist.";
		} else {
	
			$payment->setPaymentDate($data->payment_date);
			$payment->setCompany($company_data);
			$payment->setAmount($data->amount);
			$payment->setPaymentMethod($payment_method_data);
			$payment->setExternalReference($data->external_reference);
			$payment->setTerminal($data->terminal);
			$payment->setStatus($status_data);
			$payment->setReference($data->reference);
	
			$validator = $this->get('validator');
			$violations = $validator->validate($payment);
			 
			if (0 !== count($violations)) {
				foreach ($violations as $violation) {
					$mensaje .= $violation->getMessage();
				}
			}
		}
		 
		if ($mensaje == "") {
			$payment->setPaymentDate(new \DateTime($data->payment_date));
				
			try {
				$em->persist($payment);
				$em->flush();
	
				$response =  new JsonResponse($payment->getData(),200);
			} catch (\Exception $e) {
				if (strpos($e->getMessage(), 'Integrity constraint violation')) {
					$error = "There is already a payment with the following unique data external_reference => ".$data->external_reference." and payment_method =>  ".$data->payment_method;
				}else {
					$error = 'We are having problems. Please try again later';
				}
	
				$response =  new JsonResponse($this->exceptionRequest(400,$error),400);
			}
				
		} else {
			$response =  new JsonResponse($this->exceptionRequest(400,$mensaje),400);
		}
		 
		return $response;
	}
	
	/**
	 * Update Pyment by Id
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function updatePaymentByIdAction(Request $request,$id)
	{
		 
		if ($id == null) {
			$status = 400;
			$out = $this->exceptionRequest($status, "The ID is missing from the PATCH request for payment. Example -> payments/22");
		} else {
	
			$em = $this->getDoctrine()->getManager();
	
			$rep_payment = $em->getRepository("PagosBundle:Payment");
			$payment = $rep_payment->findOneBy(array('id' => $id));
	
			$data = json_decode($request->getContent());
			
			if (!empty($data)) {
				$rep_status = $em->getRepository("PagosBundle:Status");
				$status_data = $rep_status->findOneBy(array('name' => $data->status));
				
				if (!empty($payment)) {
					if(empty($status_data)){
						$status = 400;
						$out = $this->exceptionRequest($status, "Error validating fields: status does not exist.");
					}else {
						$payment->setStatus($status_data);
						$em->flush();
				
						$status = 200;
				
						$out =  $payment->getData();
					}
				}else {
					$status = 400;
					$out = $this->exceptionRequest($status, "Payment ID: ".$id." not exist.");
				}
			}else {
				$status = 400;
				$out = $this->exceptionRequest($status, "Error updating payment: no field to update.");
			}			
		}
		
		return $response =  new JsonResponse($out,$status);
		
	}
	
	/**
	 * Get All Payment and  Get Payment by ( Company, Payment Date From and Payment Date Until )
	 *
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function getpaymentsAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$rep_payment = $em->getRepository("PagosBundle:Payment");		
		
		if ($request->query->count() > 0) {
			$array_filter = (array)$request->query->getIterator();			
			
			$items = array();
			
			if (!empty( $array_filter['company']) && !empty( $array_filter['payment_date_from'] ) && !empty( $array_filter['payment_date_until'])) {

				$rep_company = $em->getRepository("PagosBundle:Company");
				$company_data = $rep_company->findOneBy(array('name' => $array_filter['company']));
				
				if (empty($company_data)){
					$status = 400;
					$out = $this->exceptionRequest($status, "Error validating fields: company does not exist.");
				}elseif ($array_filter['payment_date_until'] < $array_filter['payment_date_from']) {
					$status = 400;
					$out = $this->exceptionRequest($status, "The payment_date_until must be greater than the payment_date_from");
				} else {
					$payments_records = $rep_payment->findPaymentByCompanyRangeDate($company_data,$array_filter['payment_date_from'],$array_filter['payment_date_until']);
										
					foreach ($payments_records as $id) {
						array_push($items, $rep_payment->find($id)->getData());
					}
				}
				
			}elseif (!empty( $array_filter['company'])){
				$rep_company = $em->getRepository("PagosBundle:Company");
				$company_data = $rep_company->findOneBy(array('name' => $array_filter['company']));	
				
				$payments_records = $rep_payment->findBy(['company'=>$company_data]);
				
				foreach ($payments_records as $payment) {
					array_push($items, $payment->getData());
				}
				
			}elseif (!empty( $array_filter['payment_date_from'] ) && !empty( $array_filter['payment_date_until'])) {
				$payments_records = $rep_payment->findPaymentByRangeDate($array_filter['payment_date_from'],$array_filter['payment_date_until']);
				
				foreach ($payments_records as $id) {
					array_push($items, $rep_payment->find($id)->getData());
				}
			}					
			
		} else{
			$payments_records = $rep_payment->findAll();
			
			$items = array();
			
			foreach ($payments_records as $payment) {
				array_push($items, $payment->getData());
			}
		}	
		
		if (count($items) === 0) {
			$status = 400;
			$out = $this->exceptionRequest($status, "No payments were found with the search performed.");
				
		} else {			
			
			$status = 200;
				
			$out = array(
					"total_items" => count($items),
					"data" => $items
			);
		}
		
		return $response =  new JsonResponse($out,$status);		
	}
    
    
    private function exceptionRequest($code, $msg=null){
    	return array(
    			"Code" => $code,
    			"Message" => $msg
    	);
    }
    
}

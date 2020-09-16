<?php
namespace PagosBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use PagosBundle\Form\LoginType;

class SecurityController extends Controller
{
	/**	
	 * @Route("/login/", name="app.login")
     * @Method({"GET", "POST"})
	 */
	
	public function loginAction(Request $request)
	{
		$authenticationUtils = $this->get('security.authentication_utils');
		
		$error = $authenticationUtils->getLastAuthenticationError();		
		
		$lastUsername = $authenticationUtils->getLastUsername();
		
		$form_login = $this->createForm(LoginType::class, [
				'_username' => $lastUsername
		]);
		
		$form_login->handleRequest($request);
		
		return $this->render(
				'security/login.html.twig',
				array(
						'form_login' => $form_login->createView(),
						'error'         => $error,
				)
				);
	}
}
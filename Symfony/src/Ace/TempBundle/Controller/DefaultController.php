<?php

namespace Ace\TempBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;

class DefaultController extends Controller
{

	//TODO:email is not loaded correctly if page is refreshed
	public function optionsAction()
	{
		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

		if (!$user) {
			throw $this->createNotFoundException('No user found with username '.$name);
		}
		return $this->render('AceTempBundle:Default:options.html.twig', array('username' => $name, 'settings' => $user));
	}

	public function checkpassAction()
	{
		$response = new Response('404 Not Found!', 404, array('content-type' => 'text/plain'));		

			$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
			$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);
			$oldpass = $this->getRequest()->request->get('oldpass');

			//hash password
			$encoder_service = $this->get('security.encoder_factory');
			$encoder = $encoder_service->getEncoder($user);
			$encoded_pass = $encoder->encodePassword($oldpass, $user->getSalt());

			if($user->getPassword()===$encoded_pass)
				$response->setContent('1');
			else
				$response->setContent('0');
			$response->setStatusCode(200);
			$response->headers->set('Content-Type', 'text/html');
			return $response;		
	}

	public function checkmailAction()
	{
		$response = new Response('404 Not Found!', 404, array('content-type' => 'text/plain'));
		
			$mail = $this->getRequest()->request->get('mail');
			if($mail)
			{
				$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
				$em = $this->getDoctrine()->getEntityManager();
				$user = $em->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByEmail($mail);
				$current_user = $em->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);
				if(!$user)
					$response->setContent('1'); //email doesn't exist in database - success
				else if($user->getUsername() === $current_user->getUsername())
					$response->setContent('2'); //email is same as old one
				else
					$response->setContent('0'); //email is already in database from another user
				$response->setStatusCode(200);
				$response->headers->set('Content-Type', 'text/html');
			}
			return $response;		
	}

	//TODO:add checks for passwords
	public function setoptionsAction()
	{
		$response = new Response('404 Not Found!', 404, array('content-type' => 'text/plain'));
		
			$mydata = $this->getRequest()->request->get('data');
			if($mydata)
			{
				$fname = $mydata['firstname'];
				$lname = $mydata['lastname'];
				$mail  = $mydata['email'];
				$twitter = $mydata['tweet'];
				$oldpass = $mydata['old_pass'];
				$newpass = $mydata['new_pass'];
				$confirm_pass = $mydata['confirm_pass'];

				$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
				$em = $this->getDoctrine()->getEntityManager();
				$user = $em->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

				//update object - no checks atm
				$user->setFirstname($fname);
				$user->setLastname($lname);
				$user->setTwitter($twitter);

				//set isvalid email check
				//$emailConstraint = new Email();
				//$emailConstraint->message = 'Email address is invalid or already in use';
				//$emailConstraint->pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/';
				$emailConstraint = new Regex( array(
					'pattern' => '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
					'match' => true,
					'message' => 'Email address is invalid or already in use'
					));

				$errorList = $this->get('validator')->validateValue($mail, $emailConstraint);

				if(count($errorList)==0)
				{
					$user->setEmail($mail);
					$response->setContent('OK');
				}
				else
					$response->setContent($errorList[0]->getMessage());

				//TODO:hash the password

				if($oldpass){
					$encoder_service = $this->get('security.encoder_factory');
					$encoder = $encoder_service->getEncoder($user);
					$encoded_oldpass = $encoder->encodePassword($oldpass, $user->getSalt());
					if ($user->getPassword()===$encoded_oldpass){
						$user->setPassword($encoder->encodePassword($newpass, $user->getSalt()));
						$response->setContent('OK, Password Updated');
					}
					else
						$response->setContent('OK, Password Not Updated');
				}

				//$response->setContent('OK');
				$em->flush();

				$response->setStatusCode(200);
				$response->headers->set('Content-Type', 'text/html');
			}
			return $response;
		
	}
}

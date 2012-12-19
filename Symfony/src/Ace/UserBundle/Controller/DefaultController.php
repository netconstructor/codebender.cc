<?php

namespace Ace\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;
use MCAPI;

class DefaultController extends Controller
{
	protected $templating;
	protected $request;
	protected $ef;
	protected $sc;
	protected $em;
	protected $vd;
	protected $container;
	protected $listapi;
	protected $listid;

	public function existsAction($username)
	{
		$response = json_decode($this->getUserAction($username)->getContent(), true);
		if($response["success"])
			return new Response("true");
		else
			return new Response("false");
	}

	public function getUserAction($username)
	{
		$response = array("success" => false);
		$user = $this->em->getRepository('AceUserBundle:User')->findOneByUsername($username);
		if ($user)
		{
			$response = array("success" => true,
			"id" => $user->getId(),
			"email" => $user->getEmail(),
			"username" => $user->getUsername(),
			"firstname" => $user->getFirstname(),
			"lastname" => $user->getLastname(),
			"twitter" => $user->getTwitter()
			);
		}
		return new Response(json_encode($response));
	}

	public function getCurrentUserAction()
	{
		$response = array("success" => false);
		$current_user = $this->sc->getToken()->getUser();
		if($current_user !== "anon.")
		{
			$name = $current_user->getUsername();
			$data = json_decode($this->getUserAction($name)->getContent(), true);
			if ($data["success"] === false)
			{
				throw $this->createNotFoundException('No user found with id '.$name);
			}
			$response = $data;
		}
		return new Response(json_encode($response));

	}

	public function searchAction($token)
	{
		$results_name = json_decode($this->searchNameAction($token)->getContent(), true);
		$results_uname = json_decode($this->searchUsernameAction($token)->getContent(), true);
		$results_twit = json_decode($this->searchTwitterAction($token)->getContent(), true);
		$results = $results_name + $results_uname + $results_twit;
		return new Response(json_encode($results));
	}

	public function searchNameAction($token)
	{
		$repository = $this->em->getRepository('AceUserBundle:User');
		$users = $repository->createQueryBuilder('u')
		    ->where('u.firstname LIKE :name OR u.lastname LIKE :name')
			->setParameter('name', "%".$token."%")->getQuery()->getResult();

		$result = array();
		foreach($users as $user)
		{
			$result[] = array($user->getId() => array("firstname" => $user->getFirstname(), "lastname" => $user->getLastname(), "username" => $user->getUsername()));
		}
		return new Response(json_encode($result));
	}

	public function searchUsernameAction($token)
	{
		$repository = $this->em->getRepository('AceUserBundle:User');
		$users = $repository->createQueryBuilder('u')
		    ->where('u.username LIKE :name')
			->setParameter('name', "%".$token."%")->getQuery()->getResult();

		$result = array();
		foreach($users as $user)
		{
			$result[] = array($user->getId() => array("firstname" => $user->getFirstname(), "lastname" => $user->getLastname(), "username" => $user->getUsername()));
		}
		return new Response(json_encode($result));
	}

	public function searchTwitterAction($token)
	{
		$repository = $this->em->getRepository('AceUserBundle:User');
		$users = $repository->createQueryBuilder('u')
		    ->where('u.twitter LIKE :name')
			->setParameter('name', "%".$token."%")->getQuery()->getResult();

		$result = array();
		foreach($users as $user)
		{
			$result[] = array($user->getId() => array("firstname" => $user->getFirstname(), "lastname" => $user->getLastname(), "username" => $user->getUsername()));
		}
		return new Response(json_encode($result));
	}

	public function optionsAction()
	{
		$name = $this->sc->getToken()->getUser()->getUsername();
		$user = $this->em->getRepository('AceUserBundle:User')->findOneByUsername($name);

		if (!$user) {
			throw $this->createNotFoundException('No user found with username '.$name);
		}
		return new Response($this->templating->render('AceUserBundle:Default:options.html.twig', array('username' => $name, 'settings' => $user)));
	}

	public function checkpassAction()
	{
		$response = new Response('404 Not Found!', 404, array('content-type' => 'text/plain'));		

			$name = $this->sc->getToken()->getUser()->getUsername();
			$user = $this->em->getRepository('AceUserBundle:User')->findOneByUsername($name);
			$oldpass = $this->request->get('oldpass');

			//hash password
			$encoder_service = $this->ef;
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
		
			$mail = $this->request->get('mail');
			if($mail)
			{
				$name = $this->sc->getToken()->getUser()->getUsername();
				$user = $this->em->getRepository('AceUserBundle:User')->findOneByEmail($mail);
				$current_user = $this->em->getRepository('AceUserBundle:User')->findOneByUsername($name);
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
		
			$mydata = $this->request->get('data');
			if($mydata)
			{
				$fname = $mydata['firstname'];
				$lname = $mydata['lastname'];
				$mail  = $mydata['email'];
				$twitter = $mydata['tweet'];
				$oldpass = $mydata['old_pass'];
				$newpass = $mydata['new_pass'];
				$confirm_pass = $mydata['confirm_pass'];

				$name = $this->sc->getToken()->getUser()->getUsername();
				$user = $this->em->getRepository('AceUserBundle:User')->findOneByUsername($name);

				//update object - no checks atm
				$user->setFirstname($fname);
				$user->setLastname($lname);
				$user->setTwitter($twitter);
				
				//update user's info in newsletter mailing list
				$api = new MCAPI($this->listapi);
				$merge_vars = array("FNAME"=>$fname, "LNAME"=>$lname);
				$api->listUpdateMember($this->listid, $user->getEmail(), $merge_vars, false);
				
				$emailConstraint = new Regex( array(
					'pattern' => '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
					'match' => true,
					'message' => 'Email address is invalid or already in use'
					));

				$errorList = $this->vd->validateValue($mail, $emailConstraint);

				if(count($errorList)==0)
				{
					//update email in newsletter mailing list
					$merge_vars = array("EMAIL"=>$mail);
					$api->listUpdateMember($this->listid, $user->getEmail(), $merge_vars, false);
					$user->setEmail($mail);
					$response->setContent('Your Profile information has been updated successfully.');

				}
				else
					$response->setContent($errorList[0]->getMessage());

				//TODO:hash the password

				if($oldpass){
					$encoder_service = $this->ef;
					$encoder = $encoder_service->getEncoder($user);
					$encoded_oldpass = $encoder->encodePassword($oldpass, $user->getSalt());
					if ($user->getPassword()===$encoded_oldpass){
						$user->setPassword($encoder->encodePassword($newpass, $user->getSalt()));
						$response->setContent('Your Profile and Password information has been updated successfully.');
					}
					else
						$response->setContent('Your Profile has been updated successfully but 
						<strong><span style="color:red">there was an error</span></strong> changing
						 your Password. Please make sure that you type your <strong>Old Password</strong> correctly.');
				}

				//$response->setContent('OK');
				$this->em->flush();

				$response->setStatusCode(200);
				$response->headers->set('Content-Type', 'text/html');
			}
			return $response;
		
	}    

	public function enabledAction()
	{
		$repository = $this->em->getRepository('AceUserBundle:User');
		$users = $repository->createQueryBuilder('u')->where('u.enabled = 1')->getQuery()->getResult();
		return new Response(count($users));

	}

	public function activeAction()
	{
		$repository = $this->em->getRepository('AceUserBundle:User');
		$users = $repository->createQueryBuilder('u')->where('u.enabled = 1')->getQuery()->getResult();
		$dayofyear = new \DateTime;
		$count = 0;
		foreach($users as $user)
		{
			if($user->getLastLogin() != null)
			{
				if($dayofyear->format("z") == $user->getLastLogin()->format("z"))
					$count++;
			}
		}
		return new Response($count);

	}

	public function inlineRegisterAction()
	{
        $form = $this->container->get('fos_user.registration.form');
	    return new Response($this->templating->render('AceUserBundle:Registration:register_inline.html.twig', array(
	            'form' => $form->createView(),
	            'theme' => $this->container->getParameter('fos_user.template.theme'),
	        )));
	}

	public function __construct(EngineInterface $templating, Request $request, EncoderFactory $encoderFactory, SecurityContext $securityContext, EntityManager $entityManager, Validator $validator, ContainerInterface $container, $listapi, $listid)
	{
		$this->templating = $templating;
		$this->request = $request;
		$this->ef = $encoderFactory;
		$this->sc = $securityContext;
	    $this->em = $entityManager;
	    $this->vd = $validator;
		$this->container = $container;
		$this->listapi = $listapi;
		$this->listid = $listid;
	}

}

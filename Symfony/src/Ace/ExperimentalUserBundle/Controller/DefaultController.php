<?php

namespace Ace\ExperimentalUserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;


class DefaultController extends Controller
{
	protected $sc;
	protected $em;

	public function getCurrentUserAction()
	{
		$response = array("success" => false);
		$current_user = $this->sc->getToken()->getUser();
		if($current_user !== "anon.")
		{
			$name = $current_user->getUsername();
			$user = $this->em->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);
			if (!$user)
			{
				throw $this->createNotFoundException('No user found with id '.$name);
			}
			$response = array("success" => true, "id" => $user->getId());
		}
		return new Response(json_encode($response));

	}

	public function __construct(SecurityContext $securityContext, EntityManager $entityManager)
	{
		$this->sc = $securityContext;
	    $this->em = $entityManager;
	}
}

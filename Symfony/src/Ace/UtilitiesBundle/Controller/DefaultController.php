<?php

namespace Ace\UtilitiesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ace\UtilitiesBundle\Handler\DefaultHandler;


class DefaultController extends Controller
{
	public function newprojectAction()
	{

		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

		if (!$user)
		{
			throw $this->createNotFoundException('No user found with id '.$name);
		}

		$user = $user->getID();

		$project_name = trim(basename(stripslashes($this->getRequest()->request->get('project_name'))), ".\x00..\x20");

		if($project_name == '')
		{
			return $this->redirect($this->generateUrl('AceGenericBundle_list'));
		}

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->createAction($user, $project_name, "")->getContent();
		$response=json_decode($response, true);
		if($response["success"])
		{
			$utilities = new DefaultHandler();
			$default_text = $utilities->default_text();
			$response2 = $projectmanager->createFileAction($response["id"], $project_name.".ino", $default_text);
			$response2=json_decode($response2, true);
			if($response2["successs"])
			{
				return $this->redirect($this->generateUrl('AceGenericBundle_editor',array('id' => $response["id"])));
			}
		}

		return $this->redirect($this->generateUrl('AceGenericBundle_list'));
	}

	public function sidebarAction()
	{
		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

		if (!$user) {
			throw $this->createNotFoundException('No user found with id '.$name);
		}
		$projectmanager = $this->get('projectmanager');
		$files = $projectmanager->listAction($user->getID())->getContent();
		$files=json_decode($files, true);

		return $this->render('AceUtilitiesBundle:Default:sidebar.html.twig', array('files' => $files));
	}

}

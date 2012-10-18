<?php

namespace Ace\UtilitiesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ace\UtilitiesBundle\Handler\DefaultHandler;
use Symfony\Component\HttpFoundation\Response;


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
			if($response2["success"])
			{
				return $this->redirect($this->generateUrl('AceGenericBundle_project',array('id' => $response["id"])));
			}
		}

		return $this->redirect($this->generateUrl('AceGenericBundle_list'));
	}

	public function deleteprojectAction($id)
	{

		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

		if (!$user)
		{
			throw $this->createNotFoundException('No user found with id '.$name);
		}

		$user = $user->getID();

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->deleteAction($id)->getContent();
		$response=json_decode($response, true);
		return $this->redirect($this->generateUrl('AceGenericBundle_list'));
	}

	public function getDescriptionAction($id)
	{
		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->getDescriptionAction($id)->getContent();
		$response=json_decode($response, true);
		if($response["success"])
			return new Response($response["response"]);
		else
			return new Response("");
	}

	public function setDescriptionAction($id)
	{

		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

		if (!$user)
		{
			throw $this->createNotFoundException('No user found with id '.$name);
		}

		$user = $user->getID();
		$description = $this->getRequest()->request->get('data');

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->setDescriptionAction($id, $description)->getContent();
		return new Response("hehe");
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

	public function downloadAction($id)
	{
		$htmlcode = 200;
		$extension =".ino";
		$projectmanager = $this->get('projectmanager');

		$name = $projectmanager->getNameAction($id)->getContent();
		$name = json_decode($name, true);
		$name = $name["response"];

		$files = $projectmanager->listFilesAction($id)->getContent();
		$files = json_decode($files, true);

		if(isset($files[0]))
		{
			//TODO: We should support multi-file downloading as well
			$value = $files[0]["code"];
		}
		else
		{
			$value = "";
			$htmlcode = 404;
		}

		$headers = array('Content-Type'		=> 'application/octet-stream',
			'Content-Disposition' => 'attachment;filename="'.$name.$extension.'"');

		return new Response($value, $htmlcode, $headers);
	}

	public function saveCodeAction($id)
	{

		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

		if (!$user)
		{
			throw $this->createNotFoundException('No user found with id '.$name);
		}

		$user = $user->getID();
		$files = $this->getRequest()->request->get('data');
		$files = json_decode($files, true);

		$projectmanager = $this->get('projectmanager');
		foreach($files as $key => $file)
		{
			$response = $projectmanager->setFileAction($id, $key, htmlspecialchars_decode($file))->getContent();
			$response = json_decode($response, true);
			if($response["success"] ==  false)
				return new Response(json_encode($response));
		}
		return new Response(json_encode(array("success"=>true)));
	}
}

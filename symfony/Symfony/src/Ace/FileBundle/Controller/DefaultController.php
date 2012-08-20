<?php

namespace Ace\FileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ace\FileBundle\Document\File;

class DefaultController extends Controller
{
	const default_file = "default_text.txt";
	const directory = "../../files/";

	public function createAction()
	{
	    if ($this->getRequest()->getMethod() === 'POST')
		{
			$project_name = trim(basename(stripslashes($this->getRequest()->request->get('project_name'))), ".\x00..\x20");
			
			if($project_name == '')
			{
				return $this->redirect($this->generateUrl('AceEditorBundle_list'));
			}
			
			$file = $this->getMyProject($project_name, $error);
			if($error == -2)
			{
				$file = fopen($this::directory.$this::default_file, 'r');
				$value = fread($file, filesize($this::directory.$this::default_file));
				fclose($file);

				$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
				$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);
				
				$file = new File();
			    $file->setName($project_name);
			    $file->setCode($value);
				$timestamp = new \DateTime;
				$file->setCodeTimestamp($timestamp);
				$file->setHex("");
				$timestamp2 = new \DateTime;
				$interval = new \DateInterval('PT5M');
				$timestamp2->sub($interval);
				$file->setHexTimestamp($timestamp2);
			    $file->setOwner($user->getId());
				$file->setIsPublic(1);
				$file->setSchematic("");
				$file->setImage("");
				$file->setDescription("");

			    $dm = $this->get('doctrine.odm.mongodb.document_manager');
			    $dm->persist($file);
			    $dm->flush();

				return $this->redirect($this->generateUrl('AceEditorBundle_editor',array('project_name' => $project_name)));
				
			}
			else if($error==-1)
			{
		        throw $this->createNotFoundException('No user found with username '.$name);				
			}
			else if($error == 0)
			{
				return $this->redirect($this->generateUrl('AceEditorBundle_list'));
			}
		}
		else
	        throw $this->createNotFoundException('No POST data!');		
	}
	
	public function deleteAction($project_name)
	{
		$file = $this->getMyProject($project_name, $error);
		if(!$error)
		{
		    $dm = $this->get('doctrine.odm.mongodb.document_manager');
			$dm->remove($file);
			$dm->flush();
		}
		
		return $this->redirect($this->generateUrl('AceEditorBundle_list'));	
	}
	
	public function cloneAction($old_user, $old_project_name)
	{
		if ($this->getRequest()->getMethod() === 'POST')
		{
			$new_project_name = $this->getRequest()->request->get('name');
			
			if($new_project_name == '')
			{
				return $this->redirect($this->generateUrl('AceEditorBundle_list'));
			}
			$file = $this->getMyProject($new_project_name, $error);
			if($error == -2)
			{
				$old_file = $this->getProject($old_user, $old_project_name, $old_error);
				
				$code = $old_file->getcode();
				$description = $old_file->getDescription();
				$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
				$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);
				
				$file = new File();
			    $file->setName($new_project_name);
			    $file->setCode($code);
				$timestamp = new \DateTime;
				$file->setCodeTimestamp($timestamp);
				$file->setHex("");
				$timestamp2 = new \DateTime;
				$interval = new \DateInterval('PT5M');
				$timestamp2->sub($interval);
				$file->setHexTimestamp($timestamp2);
			    $file->setOwner($user->getId());
				$file->setIsPublic(1);
				$file->setSchematic("");
				$file->setImage("");
				$file->setDescription($description);

			    $dm = $this->get('doctrine.odm.mongodb.document_manager');
			    $dm->persist($file);
			    $dm->flush();

				return $this->redirect($this->generateUrl('AceEditorBundle_editor',array('project_name' => $new_project_name)));
				
			}
			else if($error==-1)
			{
		        throw $this->createNotFoundException('No user found with username '.$name);				
			}
			else if($error == 0)
			{
				return $this->redirect($this->generateUrl('AceEditorBundle_list'));
			}
		}
		else
	        throw $this->createNotFoundException('No POST data!');
	}
	
	public function getTimestampAction($project_name, $type)
	{
		$response = new Response('404 Not Found!', 404, array('content-type' => 'text/plain'));
		$file = $this->getMyProject($project_name, $error);
		if($type == "code" || $type == "hex")
		{
			if($type == "code")
				$response->setContent($file->getCodeTimestamp());
			else
				$response->setContent($file->getHexTimestamp());
			$response->setStatusCode(200);
			$response->headers->set('Content-Type', 'text/html');
		}
		return $response;
	}

	public function getMyCodeAction($project_name)
	{
		$file = $this->getMyProject($project_name, $error);
		if(!$error)
			return new Response($file->getCode());
		else
			return new Response("");
	}

	public function getCodeAction($username, $project_name)
	{
		$file = $this->getProject($username, $project_name, $error);
		if(!$error)
			return new Response($file->getCode());
		else
			return new Response("");
	}

	public function getMyEscapedCodeAction($project_name)
	{
		$file = $this->getMyProject($project_name, $error);
		if(!$error)
			return new Response(htmlspecialchars($file->getCode()));
		else
			return new Response("");
	}

	public function getEscapedCodeAction($username, $project_name)
	{
		$file = $this->getProject($username, $project_name, $error);
		if(!$error)
			return new Response(htmlspecialchars($file->getCode()));
		else
			return new Response("");
	}
	
	public function getMyDescriptionAction($project_name)
	{
		$file = $this->getMyProject($project_name, $error);
		if(!$error)
			return new Response($file->getDescription());
		else
			return new Response("");
	}

	public function getDescriptionAction($username, $project_name)
	{
		$file = $this->getProject($username, $project_name, $error);
		if(!$error)
			return new Response($file->getDescription());
		else
			return new Response("");
	}
	
	public function saveDescriptionAction()
    {
		$response = new Response('404 Not Found!', 404, array('content-type' => 'text/plain'));
	    if ($this->getRequest()->getMethod() === 'POST')
    	{
			$project_name = $this->getRequest()->request->get('project_name');
			$mydata = $this->getRequest()->request->get('data');
			if($project_name && $mydata)
			{
				$file = $this->getMyProject($project_name, $error);
				if(!$error)
				{
					$file->setDescription($mydata);
				    $dm = $this->get('doctrine.odm.mongodb.document_manager');
				    $dm->persist($file);
				    $dm->flush();					
					$response->setContent("OK");
					$response->setStatusCode(200);
					$response->headers->set('Content-Type', 'text/html');
				}
			}
		}
		return $response;
    }	
	
	
	public function saveCodeAction()
    {
		$response = new Response('404 Not Found!', 404, array('content-type' => 'text/plain'));
	    if ($this->getRequest()->getMethod() === 'POST')
    	{
			$project_name = $this->getRequest()->request->get('project_name');
			$mydata = $this->getRequest()->request->get('data');
			if($project_name && $mydata)
			{
				$file = $this->getMyProject($project_name, $error);
				if(!$error)
				{
					$file->setCode(htmlspecialchars_decode($mydata));
					$timestamp = new \DateTime;
					$file->setCodeTimestamp($timestamp);
				    $dm = $this->get('doctrine.odm.mongodb.document_manager');
				    $dm->persist($file);
				    $dm->flush();					
					$response->setContent("OK");
					$response->setStatusCode(200);
					$response->headers->set('Content-Type', 'text/html');
				}
			}
		}
		return $response;
    }	
	public function getMyHexAction($project_name)
	{
		$file = $this->getMyProject($project_name, $error);
		if(!$error)
			return new Response($file->getHex());
		else
			return new Response("");
	}
	
	public function saveHexAction($project_name, $data)
    {
		$response = new Response('404 Not Found!', 404, array('content-type' => 'text/plain'));
		$file = $this->getMyProject($project_name, $error);
		if(!$error)
		{
			$file->setHex($data);
			$timestamp = new \DateTime;
			$file->setHexTimestamp($timestamp);
		    $dm = $this->get('doctrine.odm.mongodb.document_manager');
		    $dm->persist($file);
		    $dm->flush();					
			$response->setContent("OK");
			$response->setStatusCode(200);
			$response->headers->set('Content-Type', 'text/html');
		}
		return $response;
    }	

	private function getMyProject($project_name, &$error)
	{
		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);
		$file = $this->getProject($name, $project_name, $error);
		return $file;
	}
    
	private function getProject($username, $project_name, &$error)
	{
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($username);
		
		if(!$user)
		{
			$error = -1;			
		}
		
		$file = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('AceFileBundle:File')
			->findOneBy(array('name' => $project_name, 'owner' => $user->getID()));
		
		if(!$file)
		{
			$error = -2;		
		}
		else
		{
			$error = 0;
			return $file;
		}		
	}
	
}

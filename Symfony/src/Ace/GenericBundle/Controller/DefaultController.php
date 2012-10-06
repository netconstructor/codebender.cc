<?php

namespace Ace\GenericBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\Regex;
use Ace\FileBundle\Document\File;
use Ace\GenericBundle\Classes\UploadHandler;


class DefaultController extends Controller
{
	
	public function indexAction()
	{
		// if($name == "tzikis")
		//	return $this->redirect($this->generateUrl('AceGenericBundle_list', array('name' => $name)));
		//
		if ($this->get('security.context')->isGranted('ROLE_USER'))
		{
			// Load user content here
			return $this->redirect($this->generateUrl('AceGenericBundle_list'));
		}

		return $this->render('AceGenericBundle:Default:index.html.twig');
	}

	public function listAction()
	{
		if (!$this->get('security.context')->isGranted('ROLE_USER'))
		{
			// Load user content here
			return $this->redirect($this->generateUrl('AceGenericBundle_homepage'));
		}


		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

		if (!$user)
		{
			throw $this->createNotFoundException('No user found with id '.$name);
		}
		$fullname= $user->getFirstname()." ".$user->getLastname()." (".$user->getUsername().") ";

		return $this->render('AceGenericBundle:Default:list.html.twig', array('name' =>$fullname));
	}	
	
	public function userAction($user)
	{
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($user);

		if (!$user) {
			return new Response('There is no such user');
		}
		$files = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('AceFileBundle:File')->findByOwner($user->getId());

		$result=@file_get_contents("http://api.twitter.com/1/statuses/user_timeline/{$user->getTwitter()}.json");
		if ( $result != false ) {
			$tweet=json_decode($result); // get tweets and decode them into a variable
			$lastTweet = $tweet[0]->text; // show latest tweet
		} else {
			$lastTweet=0;
		}
		$utilities = $this->get('utilities');
		$image = $utilities->get_gravatar($user->getEmail(),120);
		return $this->render('AceGenericBundle:Default:user.html.twig', array( 'user' => $user, 'files' => $files, 'lastTweet'=>$lastTweet, 'image'=>$image ));
	}
	
	public function projectAction($username, $project_name)
	{
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($username);
		$file = $this->get('doctrine.odm.mongodb.document_manager')->getRepository('AceFileBundle:File')
			->findOneBy(array('name' => $project_name, 'owner' => $user->getID()));

		if(!$file)
		{
			return new Response("There is no such project");
		}
		else
			return $this->render('AceGenericBundle:Default:project.html.twig', array('project'=>$project_name, 'user'=>$user));
	}
	
	public function uploadAction()
	{
						
		if ($this->getRequest()->getMethod() === 'POST')
		{	
			
			$upload_handler = new UploadHandler();			
			
			if (!preg_match('/(\.|\/)(pde|ino)$/i', $_FILES["files"]["name"][0])) 
            {
				$upload_handler->post(null);
				$response = new Response();
				$response->headers->set('Pragma', 'no-cache');
				$response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
				$response->headers->set('Content-Disposition', 'inline; filename="files.json"');
				$response->headers->set('Access-Control-Allow-Origin', '*');
				$response->headers->set('Access-Control-Allow-Methods', 'OPTIONS, HEAD, GET, POST, PUT, DELETE');
				$response->headers->set('Access-Control-Allow-Headers', 'X-File-Name, X-File-Type, X-File-Size');
				return $response;	
				
			
			}
			if (!preg_match('/^[a-z0-9\p{P}]*$/i', $_FILES["files"]["name"][0])) 
            {
				$upload_handler->post("Please use only English characters.");
				$response = new Response();
				$response->headers->set('Pragma', 'no-cache');
				$response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
				$response->headers->set('Content-Disposition', 'inline; filename="files.json"');
				$response->headers->set('Access-Control-Allow-Origin', '*');
				$response->headers->set('Access-Control-Allow-Methods', 'OPTIONS, HEAD, GET, POST, PUT, DELETE');
				$response->headers->set('Access-Control-Allow-Headers', 'X-File-Name, X-File-Type, X-File-Size');
				return $response;	
				
			
			}
			if (substr(exec("file -bi -- ".escapeshellarg($_FILES["files"]["tmp_name"][0])), 0, 4) !== 'text') 
            {
				$upload_handler->post("Filetype not allowed");
				$response = new Response();
				$response->headers->set('Pragma', 'no-cache');
				$response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
				$response->headers->set('Content-Disposition', 'inline; filename="files.json"');
				$response->headers->set('Access-Control-Allow-Origin', '*');
				$response->headers->set('Access-Control-Allow-Methods', 'OPTIONS, HEAD, GET, POST, PUT, DELETE');
				$response->headers->set('Access-Control-Allow-Headers', 'X-File-Name, X-File-Type, X-File-Size');
				return $response;	
				
			
			}							
			
			$info = pathinfo($_FILES["files"]["name"][0]);
			$file_name =  basename($_FILES["files"]["name"][0],'.'.$info['extension']);			
			$project_name = $file_name;
			
			if($project_name == '')
			{
				return $this->redirect($this->generateUrl('AceGenericBundle_list'));
			}
			
			$file = $this->getMyProject($project_name, $error);
			if($error == -2)
			{
				$upload_handler->post(null);
				
				$file = fopen($_FILES["files"]["tmp_name"][0], 'r');
				$value = fread($file, filesize($_FILES["files"]["tmp_name"][0]));
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
				
				
				$response = new Response();
				$response->headers->set('Pragma', 'no-cache');
				$response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
				$response->headers->set('Content-Disposition', 'inline; filename="files.json"');
				$response->headers->set('Access-Control-Allow-Origin', '*');
				$response->headers->set('Access-Control-Allow-Methods', 'OPTIONS, HEAD, GET, POST, PUT, DELETE');
				$response->headers->set('Access-Control-Allow-Headers', 'X-File-Name, X-File-Type, X-File-Size');				
										
				return $response;   								
			}
			else if($error==-1)
			{
		        throw $this->createNotFoundException('No user found with username '.$name);				
			}
			else if($error == 0)
			{
				return $this->redirect($this->generateUrl('AceGenericBundle_list'));
			}
			else if($error == 1)
			{
				$erroR = 'File already uploaded.';				
				$upload_handler->post($erroR);
				$response = new Response();
				$response->headers->set('Pragma', 'no-cache');
				$response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate');
				$response->headers->set('Content-Disposition', 'inline; filename="files.json"');
				$response->headers->set('Access-Control-Allow-Origin', '*');
				$response->headers->set('Access-Control-Allow-Methods', 'OPTIONS, HEAD, GET, POST, PUT, DELETE');
				$response->headers->set('Access-Control-Allow-Headers', 'X-File-Name, X-File-Type, X-File-Size');
				return $response;				
			}
			
		}
		 else if($this->getRequest()->getMethod() === 'GET')
		{	            
				return new Response('200');  // temp until i find where the fucking get is..
		}  
		else
			throw $this->createNotFoundException('No POST or GET data!');	
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
			$error = 1;
			return $file;
		}		
	}
		
	public function librariesAction()
	{
		$utilities = $this->get('utilities');
		$libraries = json_decode($utilities->get_data($this->container->getParameter('library'), 'data', "list-external"), true);
		$libraries = $libraries["list"];
		
		foreach($libraries as $key=>$library)
		{
			$libraries[$key] = array("name" => $library);
			$libinfo = json_decode($utilities->get_data($this->container->getParameter('library'), 'data', "fetch-description-external&name=".$library), true);
			$libraries[$key]["description"] =  $libinfo["description"];
			if(isset($libinfo["url"]))
				$libraries[$key]["url"] =  $libinfo["url"];
			// if(isset($libinfo["description"]))
			// 	$libinfo["description"] = $utilities->get($libinfo["description"]["url"]);
		}
		
		
		return $this->render('AceGenericBundle:Default:libraries.html.twig', array('libraries' => $libraries));
	}
	
    
}

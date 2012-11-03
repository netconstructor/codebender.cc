<?php

namespace Ace\GenericBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\Regex;
use Ace\GenericBundle\Classes\UploadHandler;
use Ace\UtilitiesBundle\Handler\DefaultHandler;



class DefaultController extends Controller
{
	
	public function indexAction()
	{
		if ($this->get('security.context')->isGranted('ROLE_USER'))
		{
			// Load user content here
			$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);
			return $this->render('AceGenericBundle:Index:list.html.twig', array('name' =>$user["username"]));
		}

		return $this->render('AceGenericBundle:Index:index.html.twig');
	}
	
	public function userAction($user)
	{
		$user = json_decode($this->get('usercontroller')->getUserAction($user)->getContent(), true);

		if ($user["success"] === false)
		{
			return $this->render('AceGenericBundle:Default:minor_error.html.twig', array('error'=> "There is no such user."));
		}

		$projectmanager = $this->get('projectmanager');
		$projects = $projectmanager->listAction($user["id"])->getContent();
		$projects = json_decode($projects, true);

		$result=@file_get_contents("http://api.twitter.com/1/statuses/user_timeline/".$user["twitter"].".json");
		if ( $result != false ) {
			$tweet=json_decode($result); // get tweets and decode them into a variable
			$lastTweet = $tweet[0]->text; // show latest tweet
		} else {
			$lastTweet=0;
		}
		$utilities = $this->get('utilities');
		$image = $utilities->get_gravatar($user["email"],120);
		return $this->render('AceGenericBundle:Default:user.html.twig', array( 'user' => $user, 'projects' => $projects, 'lastTweet'=>$lastTweet, 'image'=>$image ));
	}
	
	public function projectAction($id)
	{

		$projectmanager = $this->get('projectmanager');
		$projects = NULL;
		
		$project = json_decode($projectmanager->checkExistsAction($id)->getContent(), true);
		if($project["success"] === false)
		{
			return $this->render('AceGenericBundle:Default:minor_error.html.twig', array('error'=> "There is no such project!"));
		}

		$owner = $projectmanager->getOwnerAction($id)->getContent();
		$owner = json_decode($owner, true);
		$owner = $owner["response"];

		if ($this->get('security.context')->isGranted('ROLE_USER'))
		{
			$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

			if($owner["id"] == $user["id"])
			{
				return $this->forward('AceGenericBundle:Editor:edit', array("id"=> $id));
			}
		}

		$name = $projectmanager->getNameAction($id)->getContent();
		$name = json_decode($name, true);
		$name = $name["response"];

		$files = $projectmanager->listFilesAction($id)->getContent();
		$files = json_decode($files, true);
		$files = $files["list"];
		foreach($files as $key=>$file)
		{
			$files[$key]["code"] = htmlspecialchars($file["code"]);
		}
		
			return $this->render('AceGenericBundle:Default:project.html.twig', array('project_name'=>$name, 'owner' => $owner, 'files' => $files, "project_id" => $id));
	}
	
	public function uploadAction()
	{

		if ($this->getRequest()->getMethod() === 'POST')
		{

			$upload_handler = new UploadHandler();

			if (!preg_match('/^[a-z0-9\p{P}]*$/i', $_FILES["files"]["name"][0])){

					$info = $upload_handler->post("Invalid filename.");
					return $this->writeResponse($info);
				}
			
			
			$file_name = $_FILES["files"]["name"][0];
			$pinfo = pathinfo($_FILES["files"]["name"][0]);
			$project_name =  basename($_FILES["files"]["name"][0],'.'.$pinfo['extension']);
			$ext = $pinfo['extension'];
				
			if($ext == "ino" || $ext == "pde"){
				
				if (substr(exec("file -bi -- ".escapeshellarg($_FILES["files"]["tmp_name"][0])), 0, 4) !== 'text'){

					$info = $upload_handler->post("Filetype not allowed.");
					return $this->writeResponse($info);
				}

				 $info = $upload_handler->post(null);
				 $file = fopen($_FILES["files"]["tmp_name"][0], 'r');
				 $code = fread($file, filesize($_FILES["files"]["tmp_name"][0]));
				 fclose($file);

			     $sketch_id = $this->createUploadedProject($project_name/*, $code , $sketch_id */);
					if(isset($sketch_id)){
						if(!$this->createUploadedFile($sketch_id, $project_name, $code)){
							$info = $upload_handler->post("Error creating file.");
							return $this->writeResponse($info);
						}
					}else {
							$info = $upload_handler->post("Error creating Project.");
							return $this->writeResponse($info);
					}

				$updated_info = array();
				$updated_info[] = $this->fixFile($info, $sketch_id, $project_name, $ext);

				return $this->writeResponse($updated_info);
			}
			else if($ext == "zip"){

				$info = $upload_handler->post(null);

				$code = '';
			     $z = new \ZipArchive();
				 $headers = array();
				 $cpps = array();
				 $count = 0;

				 if ($z->open($_FILES["files"]["tmp_name"][0])) {

					 for ($i = 0; $i < $z->numFiles; $i++) {

						$nameIndex = $z->getNameIndex($i);

						if (!preg_match('/^[a-z0-9\p{P}]*$/i', $nameIndex)){
				
					     $info = $upload_handler->post("Invalid filename.");
						 return $this->writeResponse($info);
						}

						$exp = explode('.', $nameIndex);
						$exp2 = explode('/', $nameIndex);
						$ext2 = end($exp);
						$end = end($exp2);
						// $folderName = prev($exp2);
						// $fileName = basename($end,".pde");

						 if( $ext2 == "pde"){
							 //if( $folderName == $fileName || count($exp) == 2)
						     if(mb_detect_encoding($z->getFromIndex($i), 'UTF-8', true) !== FALSE){
								$count++;
								$code = $z->getFromIndex($i);
								$project_name = $end;
							 }
						 } else if($ext2 == "ino" /*&& count($exp) == 2*/){

								if(mb_detect_encoding($z->getFromIndex($i), 'UTF-8', true) !== FALSE){
								$count++;
								$code = $z->getFromIndex($i);
								$project_name = $end;
							 }
						 } else if($ext2 == "h"){
								$headers[$end] = $z->getFromIndex($i);
						 }
						 else if($ext2 == "cpp"){
								$cpps[$end] = $z->getFromIndex($i);
						 }						 
						 // $code .= $z->getNameIndex($i)."\r\n";
					}

				} else {$code = 'ERROR opening file';}
				
			if($count == 1){
				
				if(mb_detect_encoding($code, 'UTF-8', true) !== FALSE){
					$sketch_id = $this->createUploadedProject($project_name);
					if(isset($sketch_id)){
						if(!$this->createUploadedFile($sketch_id, $project_name, $code)){
							$info = $upload_handler->post("Error creating file.");
							return $this->writeResponse($info);
						}
					}else {
							$info = $upload_handler->post("Error creating Project.");
							return $this->writeResponse($info);
					}
				} else {
						$info = $upload_handler->post("Filetype not allowed.");
						return $this->writeResponse($info);
				}
				
				foreach($headers as $key => $value){

					if(mb_detect_encoding($value, 'UTF-8', true) !== FALSE){
						if(!$this->createUploadedFile($sketch_id, $key, $value)){
							$info = $upload_handler->post("Error creating file.");
							return $this->writeResponse($info);
						}
					}
				}

				foreach($cpps as $key => $value){

					if(mb_detect_encoding($value, 'UTF-8', true) !== FALSE){
						if(!$this->createUploadedFile($sketch_id, $key, $value)){
							$info = $upload_handler->post("Error creating file.");
							return $this->writeResponse($info);
						}
					}
				}
				
			} else {
					$sketch_id = null;
				}

				$updated_info = array();
				$updated_info[] = $this->fixFile($info, $sketch_id, $project_name, $ext);

				return $this->writeResponse($updated_info);

			}else {
				$info = $upload_handler->post(null);
				return $this->writeResponse($info);
			}
			
		}
		 else if($this->getRequest()->getMethod() === 'GET')
		{	            
				return new Response('200');  // temp until i find where the fucking get is..
		}  
		else
			throw $this->createNotFoundException('No POST or GET data!');	
	}


	public function fixFile($info, $sketch_id, $project_name, $ext)
	{
		$File = new \stdClass();

		$vars = get_object_vars($info[0]);

		if(isset($sketch_id)){

		foreach($vars as $name => $value) {
			if($name == 'url'){
				$File->$name = $value.$sketch_id;
			}else if ($name == 'name' && $ext == "zip"){
				$File->$name = $project_name;
			}else{
				$File->$name = $value;
			}
		}
		}else {$File->error = "Failed to create Project.";}

		return $File;
	}

	public function writeResponse($info)
	{
		header('Vary: Accept');
		$json = json_encode($info);
		$redirect = isset($_REQUEST['redirect']) ?
		stripslashes($_REQUEST['redirect']) : null;
		if ($redirect) {
               header('Location: '.sprintf($redirect, rawurlencode($json)));
			return;
		}
		if (isset($_SERVER['HTTP_ACCEPT']) &&
		(strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
		header('Content-type: application/json');
		} else {
			header('Content-type: text/plain');
		}

		return new Response($json);
	}

	public function createUploadedFile($id, $filename, $code)
	{		
		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->createFileAction($id, $filename, $code)->getContent();
		$response = json_decode($response, true);						
		
		return $response["success"];
	}


	public function createUploadedProject($file_name)
	{
		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$exp = explode(".", $file_name);
		$project_name =  $exp[0];

			$projectmanager = $this->get('projectmanager');
			$response1 = $projectmanager->createAction($user["id"], $project_name, "")->getContent();
			$response1=json_decode($response1, true);
			if($response1["success"]){
				$sketch_id = $response1["id"];
			} else {
				$sketch_id = null;
			}
		return $sketch_id;
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
		}
		
		
		return $this->render('AceGenericBundle:Default:libraries.html.twig', array('libraries' => $libraries));
	}
	
	public function boardsAction()
	{
		$boardcontroller = $this->get('boardcontroller');
		$boards = json_decode($boardcontroller->listAction()->getContent(), true);
		return $this->render('AceGenericBundle:Default:boards.html.twig', array('boards' => $boards));
	}
    
}

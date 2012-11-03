<?php

namespace Ace\UtilitiesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ace\UtilitiesBundle\Handler\DefaultHandler;
use Symfony\Component\HttpFoundation\Response;
use Ace\UtilitiesBundle\Handler\UploadHandler;


class DefaultController extends Controller
{
	public function newprojectAction()
	{

		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$project_name = trim(basename(stripslashes($this->getRequest()->request->get('project_name'))), ".\x00..\x20");

		if($project_name == '')
		{
			return $this->redirect($this->generateUrl('AceGenericBundle_index'));
		}

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->createAction($user["id"], $project_name, "")->getContent();
		$response=json_decode($response, true);
		if($response["success"])
		{
			$utilities = new DefaultHandler();
			$default_text = $utilities->default_text();
			$response2 = $projectmanager->createFileAction($response["id"], $project_name.".ino", $default_text)->getContent();
			$response2=json_decode($response2, true);
			if($response2["success"])
			{
				return $this->redirect($this->generateUrl('AceGenericBundle_project',array('id' => $response["id"])));
			}
		}

		return $this->redirect($this->generateUrl('AceGenericBundle_index'));
	}

	public function deleteprojectAction($id)
	{

		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->deleteAction($id)->getContent();
		$response=json_decode($response, true);
		return $this->redirect($this->generateUrl('AceGenericBundle_index'));
	}

	public function listFilenamesAction($id, $show_ino)
	{
		$projectmanager = $this->get('projectmanager');
		$files = $projectmanager->listFilesAction($id)->getContent();
		$files=json_decode($files, true);

		if($show_ino == 0)
		{
			foreach($files as $key=>$file)
			if(strpos($file['filename'], ".ino") !== FALSE)
			{
				unset($files[$key]);
			}
		}

		return $this->render('AceUtilitiesBundle:Default:list_filenames.html.twig', array('files' => $files));
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

		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$description = $this->getRequest()->request->get('data');

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->setDescriptionAction($id, $description)->getContent();
		return new Response("hehe");
	}

	public function setNameAction($id)
	{

		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$new_name = $this->getRequest()->request->get('data');

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->renameAction($id, $new_name)->getContent();
		return new Response($response);
	}

	public function renameFileAction($id)
	{

		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$old_filename = $this->getRequest()->request->get('oldFilename');
		$new_filename = $this->getRequest()->request->get('newFilename');

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->renameFileAction($id, $old_filename, $new_filename)->getContent();
		return new Response($response);
	}

	public function sidebarAction()
	{
		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$projectmanager = $this->get('projectmanager');
		$files = $projectmanager->listAction($user["id"])->getContent();
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

		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

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

	public function cloneAction($id)
	{

		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$name = $this->getRequest()->request->get('name');

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->cloneAction($user["id"], $id)->getContent();
		$response = json_decode($response, true);
		return $this->redirect($this->generateUrl('AceGenericBundle_project',array('id' => $response["id"])));
	}

	public function createFileAction($id)
	{
		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$data = $this->getRequest()->request->get('data');
		$data = json_decode($data, true);

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->createFileAction($id, $data["filename"], "");
		$response = json_decode($response, true);
		if($response["success"] ==  false)
			return new Response(json_encode($response));
		return new Response(json_encode(array("success"=>true)));
	}

	public function deleteFileAction($id)
	{
		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$data = $this->getRequest()->request->get('data');
		$data = json_decode($data, true);

		$projectmanager = $this->get('projectmanager');
		$response = $projectmanager->deleteFileAction($id, $data["filename"]);
		$response = json_decode($response, true);
		if($response["success"] ==  false)
			return new Response(json_encode($response));
		return new Response(json_encode(array("success"=>true)));
	}

	public function imageAction()
	{
		$user = json_decode($this->get('usercontroller')->getCurrentUserAction()->getContent(), true);

		$utilities = $this->get('utilities');
		$image = $utilities->get_gravatar($user["email"]);

		return $this->render('AceUtilitiesBundle:Default:image.html.twig', array('user' => $user["email"],'image' => $image));
	}


	public function uploadAction()
	{

		if ($this->getRequest()->getMethod() === 'POST')
		{

			$upload_handler = new UploadHandler(null, null, $this);

			if (!preg_match('/^[a-z0-9\p{P}]*$/i', $_FILES["files"]["name"][0])){

					$info = $upload_handler->post("Invalid filename.");
					return $upload_handler->writeResponse($info);
				}

			$file_name = $_FILES["files"]["name"][0];
			$pinfo = pathinfo($_FILES["files"]["name"][0]);
			$project_name =  basename($_FILES["files"]["name"][0],'.'.$pinfo['extension']);
			$ext = $pinfo['extension'];

			if($ext == "ino" || $ext == "pde"){

				if (substr(exec("file -bi -- ".escapeshellarg($_FILES["files"]["tmp_name"][0])), 0, 4) !== 'text'){

					$info = $upload_handler->post("Filetype not allowed.");
					return $upload_handler->writeResponse($info);
				}

				 $info = $upload_handler->post(null);
				 $file = fopen($_FILES["files"]["tmp_name"][0], 'r');
				 $code = fread($file, filesize($_FILES["files"]["tmp_name"][0]));
				 fclose($file);

			     $sketch_id = $upload_handler->createUploadedProject($project_name);
					if(isset($sketch_id)){
						if(!$upload_handler->createUploadedFile($sketch_id, $project_name, $code)){
							$info = $upload_handler->post("Error creating file.");
							return $upload_handler->writeResponse($info);
						}
					}else {
							$info = $upload_handler->post("Error creating Project.");
							return $upload_handler->writeResponse($info);
					}

				$updated_info = array();
				$updated_info[] = $upload_handler->fixFile($info, $sketch_id, $project_name, $ext);

				return $upload_handler->writeResponse($updated_info);
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
						 return $upload_handler->writeResponse($info);
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
					$sketch_id = $upload_handler->createUploadedProject($project_name);
					if(isset($sketch_id)){
						if(!$upload_handler->createUploadedFile($sketch_id, $project_name, $code)){
							$info = $upload_handler->post("Error creating file.");
							return $upload_handler->writeResponse($info);
						}
					}else {
							$info = $upload_handler->post("Error creating Project.");
							return $upload_handler->writeResponse($info);
					}
				} else {
						$info = $upload_handler->post("Filetype not allowed.");
						return $upload_handler->writeResponse($info);
				}

				foreach($headers as $key => $value){

					if(mb_detect_encoding($value, 'UTF-8', true) !== FALSE){
						if(!$upload_handler->createUploadedFile($sketch_id, $key, $value)){
							$info = $upload_handler->post("Error creating file.");
							return $upload_handler->writeResponse($info);
						}
					}
				}

				foreach($cpps as $key => $value){

					if(mb_detect_encoding($value, 'UTF-8', true) !== FALSE){
						if(!$upload_handler->createUploadedFile($sketch_id, $key, $value)){
							$info = $upload_handler->post("Error creating file.");
							return $upload_handler->writeResponse($info);
						}
					}
				}

			} else {
					$sketch_id = null;
				}

				$updated_info = array();
				$updated_info[] = $upload_handler->fixFile($info, $sketch_id, $project_name, $ext);

				return $upload_handler->writeResponse($updated_info);

			}else {
				$info = $upload_handler->post(null);
				return $upload_handler->writeResponse($info);
			}

		}
		 else if($this->getRequest()->getMethod() === 'GET')
		{
				return new Response('200');  // temp until i find where the fucking get is..
		}
		else
			throw $this->createNotFoundException('No POST or GET data!');
	}

}

<?php

namespace Ace\GenericBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\Constraints\Regex;
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
	
	public function projectAction($id, $embed = false)
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

		if (!$embed && $this->get('security.context')->isGranted('ROLE_USER'))
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

		$json = array("project" => array("name" => $name, "url" => $this->get('router')->generate('AceGenericBundle_project',array("id" => $id), true)),"user"=>array("name"=>$owner["username"], "url" => $this->get('router')->generate('AceGenericBundle_user',array('user' => $owner['username']), true )), "download_url" => $this->get('router')->generate('AceUtilitiesBundle_download',array('id'=> $id), true), "files" => $files);
		$json = json_encode($json);

		if($embed)
			return $this->render('AceGenericBundle:Default:project_embeddable.html.twig', array("json" => $json));
		return $this->render('AceGenericBundle:Default:project.html.twig', array('project_name'=>$name, 'owner' => $owner, 'files' => $files, "project_id" => $id, "json" => $json));
	}
	
	public function librariesAction()
	{
		$utilities = $this->get('utilities');

		$libraries = json_decode($utilities->get($this->container->getParameter('library')."?data=all"), true);
		$categories = $libraries["categories"];

		return $this->render('AceGenericBundle:Default:libraries.html.twig', array('categories' => $categories));
	}

	public function exampleAction($library, $example, $url)
	{
		$utilities = $this->get('utilities');
		$data = htmlspecialchars($utilities->get($url));
		$file = array("filename" => $example.".ino", "code" => $data);
		$files = array($file);
		return $this->render('AceGenericBundle:Default:example.html.twig', array('library' => $library, 'example' => $example, 'files' => $files));
	}
	
	public function boardsAction()
	{
		$boardcontroller = $this->get('boardcontroller');
		$boards = json_decode($boardcontroller->listAction()->getContent(), true);
		return $this->render('AceGenericBundle:Default:boards.html.twig', array('boards' => $boards));
	}
    
}

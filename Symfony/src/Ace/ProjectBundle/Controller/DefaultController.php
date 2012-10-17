<?php

namespace Ace\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ace\ProjectBundle\Entity\Project as Project;
use Doctrine\ORM\EntityManager;
use Ace\ProjectBundle\Controller\MongoFilesController;

class DefaultController extends Controller
{
    protected $em;
	protected $mfc;

	public function listAction($owner)
	{
		$projects = $this->em->getRepository('AceProjectBundle:Project')->findByOwner($owner);
		$list = array();
		foreach($projects as $project)
		{
			$list[] = array("id"=> $project->getId(), "name"=>$project->getName(), "description"=>$project->getDescription(), "is_public"=>$project->getIsPublic());
		}
		return new Response(json_encode($list));
	}

	public function createAction($owner, $name, $description)
	{
		$project = new Project();
		$user = $this->em->getRepository('AceExperimentalUserBundle:ExperimentalUser')->find($owner);
		$project->setOwner($user);
	    $project->setName($name);
	    $project->setDescription($description);
	    $project->setIsPublic(TRUE);
	
		$project->setType("mongo");
		$mongo = $this->mfc;
		$id = $mongo->createAction();
		
		$project->setProjectfilesId($id);

	    $em = $this->em;
	    $em->persist($project);
	    $em->flush();

	    return new Response(json_encode(array("success" => true, "id" => $project->getId())));
	}
	
	public function deleteAction($id)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$deletion = $mongo->deleteAction($project->getProjectfilesId());
		if($deletion === 0)
		{
		    $em = $this->em;
			$em->remove($project);
			$em->flush();
			return new Response(json_encode(array("success" => true)));
		}
		else
		{
			return new Response(json_encode(array("success" => false, "id" => $project->getProjectfilesId())));
		}
		
	}
	
	public function listFilesAction($id)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$list = $mongo->listFilesAction($project->getProjectfilesId());
		return new Response(json_encode($list));
	}

	public function createFileAction($id, $filename, $code)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$create = $mongo->createFileAction($project->getProjectfilesId(), $filename, $code);
		return new Response(json_encode($create));
	}
	
	public function getFileAction($id, $filename)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$get = $mongo->getFileAction($project->getProjectfilesId(), $filename);
		return new Response(json_encode($get));
		
	}
	
	public function setFileAction($id, $filename, $code)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$set = $mongo->setFileAction($project->getProjectfilesId(), $filename, $code);
		return new Response(json_encode($set));
		
	}
		
	public function deleteFileAction($id, $filename)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$delete = $mongo->deleteFileAction($project->getProjectfilesId(), $filename);
		return new Response(json_encode($delete));
	}
	
	public function getBinaryAction($id, $flags)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$getBinary = $mongo->getBinaryAction($project->getProjectfilesId(), $flags);
		return new Response(json_encode($getBinary));
	}

	public function setBinaryAction($id, $flags, $bin)
	{
		$project = $this->getProjectById($id);
		$mongo = $this->mfc;
		$setBinary = $mongo->setBinaryAction($project->getProjectfilesId(), $flags, $bin);
		return new Response(json_encode($setBinary));
	}

	public function getProjectById($id)
	{
		$em = $this->em;
		$project = $this->em->getRepository('AceProjectBundle:Project')->find($id);
	    if (!$project)
	        throw $this->createNotFoundException('No project found with id '.$id);			
			// return new Response(json_encode(array(false, "Could not find project with id: ".$id)));
		
		return $project;
	}

	public function __construct(EntityManager $entityManager, MongoFilesController $mongoFilesController)
	{
	    $this->em = $entityManager;
		$this->mfc = $mongoFilesController;
	}
}

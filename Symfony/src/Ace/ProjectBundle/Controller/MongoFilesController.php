<?php
// src/Ace/ProjectBundle/Controller/MongoFilesController.php

namespace Ace\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ace\ProjectBundle\Document\ProjectFiles;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ODM\MongoDB\DocumentManager;

class MongoFilesController extends Controller
{
    protected $dm;

	public function createAction()
	{
	    $pf = new ProjectFiles();
	    $pf->setFiles(array());
		$pf->setImages(array());
		$pf->setSketches(array());
		$pf->setBinaries(array());
		
	    $dm = $this->dm;
	    $dm->persist($pf);
	    $dm->flush();

	    return serialize($pf->getId());
	}
	
	public function deleteAction($id)
	{
		$pf = $this->getProjectById($id);
	    $dm = $this->dm;
		$dm->remove($pf);
		$dm->flush();
		return 0;
	}
	
	public function listFilesAction($id)
	{
		$pf = $this->getProjectById($id);
		
		$list = $pf->getFiles();
		return $list;
	}
	
	public function createFileAction($id, $filename, $code)
	{
		$list = $this->listFilesAction($id);
		foreach($list as $file)
		{
			if($file["filename"] == $filename)
				return json_encode(array("success" => false, "id" => $id, "filename" => $filename));
		}
		$list[] = array("filename"=> $filename, "code" => $code);
		$this->setFilesById($id, $list);
		return json_encode(array("success" => true));
	}
	
	public function getFileAction($id, $filename)
	{
		$list = $this->listFilesAction($id);
		foreach($list as $file)
		{
			if($file["filename"] == $filename)
				return $file["code"];
		}
		return false;
	}
	
	public function setFileAction($id, $filename, $code)
	{
		$list = $this->listFilesAction($id);
		foreach($list as &$file)
		{
			if($file["filename"] == $filename)
			{
				$file["code"] = $code;
				$this->setFilesById($id, $list);
				return json_encode(array("success" => true));
			}
		}
		return json_encode(array("success" => false));
		
	}
	
	public function deleteFileAction($id, $filename)
	{
		$list = $this->listFilesAction($id);
		foreach($list as $key=>$file)
		{
			if($file["filename"] == $filename)
			{
				unset($list[$key]);
				$this->setFilesById($id, $list);
				return json_encode(array("success" => true));
			}
		}
		return json_encode(array("success" => false, "id" => $id, "filename" => $filename));
	}

		public function getBinaryAction($id, $flags)
		{
			$pf = $this->getProjectById($id);
			$binaries = $pf->getBinaries();
			if(isset($binaries[$flags]))
				return $binaries[$flags];
			else
				return false;
		}

		public function setBinaryAction($id, $flags, $bin)
		{
			$pf = $this->getProjectById($id);
			$binaries = $pf->getBinaries();
			$binaries[$flags] = array("binary"=>$bin, "timestamp"=>new \DateTime);
			$pf->setBinaries($binaries);
		    $dm = $this->dm;
		    $dm->persist($pf);
		    $dm->flush();
			return true;
		}
	
	public function getProjectById($id)
	{
	    $dm = $this->dm;
		$pf = $dm->getRepository('AceProjectBundle:ProjectFiles')->find(unserialize($id));
		if(!$pf)
		{
	        throw $this->createNotFoundException('No projectfiles found with id: '.$id);
		}
		
		return $pf;
	}

	public function setFilesById($id, $files)
	{
		$pf = $this->getProjectById($id);
		$pf->setFiles($files);
		$pf->setFilesTimestamp(new \DateTime);
	    $dm = $this->dm;
	    $dm->persist($pf);
	    $dm->flush();
	}

	public function __construct(DocumentManager $documentManager)
	{
	    $this->dm = $documentManager;
	}
}


<?php

namespace Ace\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ace\ProjectBundle\Entity\Project as Project;
use Doctrine\ORM\EntityManager;
use Ace\ProjectBundle\Controller\MongoFilesController;

class LibraryController extends ProjectController
{
    protected $em;
	protected $fc;
    protected $sl;



//    public function createprojectAction($user_id, $project_name, $code)
//
//
//    public function cloneAction($owner, $id)
//
//
//    public function renameAction($id, $new_name)
//
//
//    protected function canCreateFile($id, $filename)
//



    public function createprojectAction($user_id, $project_name, $code)
	{
        $retval;
        $response = parent::createprojectAction($user_id, $project_name, $code)->getContent();
        $response=json_decode($response, true);
		if($response["success"])
		{
			$response2 = $this->createFileAction($response["id"], $project_name.".h", $code)->getContent();
			$response2=json_decode($response2, true);
			if($response2["success"])
			{
                $response3 = $this->createFileAction($response["id"], $project_name.".cpp", $code)->getContent();
                $response3=json_decode($response3, true);
                if($response3["success"])
                {
                    $retval = array("success" => true, "id" => $response["id"]);
                }
			}
			else
				$retval = $response2;
		}
		else
			$retval = $response;

		return new Response(json_encode($retval));
	}


	public function cloneAction($owner, $id)
	{
        $response = json_decode(parent::cloneAction($owner, $id)->getContent(), true);
        $old_name = json_decode($this->getNameAction($id)->getContent(),true);
        $old_name = $old_name["response"];
        if($response["success"] == true)
        {

            foreach($response["list"] as $file)
            {
                if(pathinfo($file["filename"], PATHINFO_FILENAME) == $old_name)
                {
                    if(pathinfo($file["filename"], PATHINFO_EXTENSION)== "cpp")
                        $this->createFileAction($response["id"],$response["name"].".cpp",$file["code"]);
                    else if(pathinfo($file["filename"], PATHINFO_EXTENSION)== "h")
                         $this->createFileAction($response["id"],$response["name"].".h",$file["code"]);
                }
                else
                {
                    $this->createFileAction($response["id"],$file["filename"],$file["code"]);
                }
            }
            return new Response(json_encode(array("success" => true, "id" => $response["id"])));
        }
        else
        {
            return new Response(json_encode(array("success" => false, "id" => $id)));
        }


    }

    public function renameAction($id, $new_name)
    {
        $response = json_decode(parent::renameAction($id, $new_name)->getContent(),true);
        if($response["success"])
        {
            $output = array("success" => true);

            $project = $this->getProjectById($id);
            $old_name = $project->getName();

            $project->setName($new_name);
            $em = $this->em;
            $em->persist($project);
            $em->flush();

            foreach($response["list"] as $file)
            {
                if(pathinfo($file["filename"], PATHINFO_FILENAME) == $old_name)
                {
                    if(pathinfo($file["filename"], PATHINFO_EXTENSION)== "cpp")
                    {
                        $rnm = json_decode($this->renameFileAction($id,$old_name.".cpp",$new_name.".cpp")->getContent(),true);
                    }
                    else if(pathinfo($file["filename"], PATHINFO_EXTENSION)== "h")
                    {
                        $rnm = json_decode( $this->renameFileAction($id,$old_name.".h",$new_name.".h")->getContent(),true);
                    }
                }
            }

            return new Response(json_encode($output));
        }

        return new Response(json_encode($response));

    }

    protected function canCreateFile($id, $filename)
    {
        return json_encode(array("success" => true));
    }

	public function __construct(EntityManager $entityManager, DiskFilesController $diskFilesController)
	{
	    $this->em = $entityManager;
        $this->fc = $diskFilesController;
        $this->sl = "disk";
	}
}

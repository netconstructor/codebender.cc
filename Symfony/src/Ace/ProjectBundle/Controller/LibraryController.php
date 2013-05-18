<?php

namespace Ace\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ace\ProjectBundle\Entity\Library as Library;
use Doctrine\ORM\EntityManager;
use Ace\ProjectBundle\Controller\MongoFilesController;
use Symfony\Component\Security\Core\SecurityContext;

class LibraryController extends ProjectController
{
    protected $em;
	protected $fc;
    protected $sl;
    protected $sc;


    public function createprojectAction($user_id, $project_name, $code, $isPublic = true)
	{
        $retval;

        $canCreate = json_decode($this->canCreatePersonalLibrary($user_id),true);

        if($canCreate["success"])
        {
            $response = json_decode($this->createAction($user_id, $project_name, "", $isPublic)->getContent(), true);
        }
        else
        {
            $response = $canCreate;
        }

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

    public function createAction($owner, $name, $description, $isPublic)
    {
        $validName = json_decode($this->nameIsValid($name), true);
        if(!$validName["success"])
            return new Response(json_encode($validName));

        $project=new Library();
        $user = $this->em->getRepository('AceUserBundle:User')->find($owner);
        $project->setOwner($user);
        $project->setName($name);
        $project->setDescription($description);
        $project->setIsPublic($isPublic);
        $project->setType($this->sl);
        $response = json_decode($this->fc->createAction(), true);

        if($response["success"])
        {
            $id = $response["id"];
            $project->setProjectfilesId($id);

            $em = $this->em;
            $em->persist($project);
            $em->flush();

            return new Response(json_encode(array("success" => true, "id" => $project->getId())));
        }
        else
            return new Response(json_encode(array("success" => false, "owner_id" => $user->getId(), "name" => $name)));
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

    protected function canCreatePersonalLibrary($owner)
    {
        $libs = $this->em->getRepository('AceProjectBundle:Library')->findByOwner($owner);
        $currentLibs = count($libs);

        $prs= $this->em->getRepository('AceProjectBundle:PersonalLibraries')->findByOwner($owner);
        $maxPersonal = 0;
        foreach ($prs as $p)
        {
            $now = new \DateTime("now");
            if($now>= $p->getStarts() && ($p->getExpires()==NULL || $now < $p->getExpires()))
                $maxPersonal+=$p->getNumber();
        }

        if($currentLibs >= $maxPersonal)
            return json_encode(array("success" => false, "error" => "Cannot create personal library."));
        else
            return json_encode(array("success" => true, "available" => $maxPersonal - $currentLibs));

    }

    protected function canCreateFile($id, $filename)
    {
        return json_encode(array("success" => true));
    }

    protected function getProjectsRepository()
    {
        return $this->em->getRepository('AceProjectBundle:Library');
    }

	public function __construct(EntityManager $entityManager, DiskFilesController $diskFilesController,  SecurityContext $securitycontext)
	{
	    $this->em = $entityManager;
        $this->fc = $diskFilesController;
        $this->sl = "disk";
        $this->sc = $securitycontext;
	}
}

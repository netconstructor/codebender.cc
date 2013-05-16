<?php

namespace Ace\BoardBundle\Controller;

use Ace\BoardBundle\Entity\Board;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\ContainerInterface;


class DefaultController extends Controller
{

    protected $em;
    protected $sc;
    protected $container;

	public function listAction()
	{
		header('Access-Control-Allow-Origin: *');

		$boards = array();

        if ($this->get('security.context')->isGranted('ROLE_USER'))
        {
            $user = json_decode($this->get('ace_user.usercontroller')->getCurrentUserAction()->getContent(), true);

            $db_boards = $this->em->getRepository('AceBoardBundle:Board')->findByOwner($user["id"]);

            foreach ($db_boards as $board)
            {
                $boards[] = array(
                    "name" => $board->getName(),
                    "upload" => json_decode($board->getUpload(), true),
                    "bootloader" => json_decode($board->getBootloader(), true),
                    "build" => json_decode($board->getBuild(), true),
                    "description" => $board->getDescription(),
                    "personal" => true,
                    "id" => $board->getId()
                );
            }
        }

		$db_boards = $this->em->getRepository('AceBoardBundle:Board')->findBy(array("owner" => null));

		foreach ($db_boards as $board)
		{
			$boards[] = array(
				"name" => $board->getName(),
				"upload" => json_decode($board->getUpload(), true),
				"bootloader" => json_decode($board->getBootloader(), true),
				"build" => json_decode($board->getBuild(), true),
				"description" => $board->getDescription(),
				"personal" => false,
                "id" => $board->getId()
			);
		}


		return new Response(json_encode($boards));
	}

    public function addBoardAction()
    {
        if($_FILES["boards"]["error"]>0)
        {
            $this->container->get('session')->setFlash("error","Error: Upload failed with error code ".$_FILES["boards"]["error"].".");
            return $this->redirect($this->generateUrl("AceGenericBundle_boards"));
        }
        if($_FILES["boards"]["type"]!== "text/plain")
        {
            $this->container->get('session')->setFlash("error","Error: File type should be .txt.");
            return $this->redirect($this->generateUrl("AceGenericBundle_boards"));
        }
        $current_user = $this->sc->getToken()->getUser();
        $canAdd = json_decode($this->canAddPersonalBoard($current_user->getId()), true);
        if(!$canAdd["success"])
        {
            $this->container->get('session')->setFlash("error","Error: Cannot add personal board.");
            return $this->redirect($this->generateUrl("AceGenericBundle_boards"));
        }

        $available = $canAdd["available"];
        $boards = $this->parse_properties(file_get_contents( $_FILES["boards"]["tmp_name"]));

        if(count($boards)>$available)
        {
            $this->container->get('session')->setFlash("error","Error: You can add up to ".$available." boards (tried to add ".count($boards).").");
            return $this->redirect($this->generateUrl("AceGenericBundle_boards"));
        }

        foreach ($boards as $b)
        {
            if(!(isset($b['name']) && isset($b["upload"]) && isset($b["bootloader"]) && isset($b["build"])))
            {
                $this->container->get('session')->setFlash("error","Error: File does not have the required structure.");
                return $this->redirect($this->generateUrl("AceGenericBundle_boards"));
            }
            $board = new Board();
            $board->setName($b["name"]);
            $board->setUpload(json_encode($b["upload"]));
            $board->setBootloader(json_encode($b["bootloader"]));
            $board->setBuild(json_encode($b["build"]));
            $board->setOwner($current_user);
            $board->setDescription("Personal Board");

            $this->em->persist($board);

        }
        $this->em->flush();
        $this->container->get('session')->setFlash("notice",count($boards)." boards were successfully added.");
        return $this->redirect($this->generateUrl("AceGenericBundle_boards"));
    }

    public function canAddPersonalBoardAction($owner)
    {
        return new Response($this->canAddPersonalBoard($owner));
    }

    protected function parse_properties($txtProperties) {
        $result = array();
        $lines = explode("\n", $txtProperties);
        $key = "";
        $isWaitingOtherLine = false;
        foreach ($lines as $i => $line) {
            if (empty($line) || (!$isWaitingOtherLine && strpos($line, "#") === 0))
                continue;

            if (!$isWaitingOtherLine) {
                $key = substr($line, 0, strpos($line, '='));
                $value = substr($line, strpos($line, '=')+1, strlen($line));
            }
            else {
                $value .= $line;
            }

            /* Check if ends with single '\' */
            if (strrpos($value, "\\") === strlen($value)-strlen("\\")) {
                $value = substr($value,0,strlen($value)-1)."\n";
                $isWaitingOtherLine = true;
            }
            else {
                $isWaitingOtherLine = false;
            }

            $keys = explode(".", $key);
            $local_result = $value;
            for($i = count($keys) -1; $i >= 0; $i--)
            {
                $local_result = array($keys[$i] => $local_result);
            }

            $result = array_merge_recursive($result, $local_result);
            unset($lines[$i]);
        }

        return $result;
    }

    protected function canAddPersonalBoard($owner)
    {
        $boards = $this->em->getRepository('AceBoardBundle:Board')->findByOwner($owner);
        $currentPersonal = count($boards);


        $prs= $this->em->getRepository('AceBoardBundle:PersonalBoards')->findByOwner($owner);
        $maxPersonal = 0;
        foreach ($prs as $p)
        {
            $now = new \DateTime("now");
            if($now>= $p->getStarts() && ($p->getExpires()==NULL || $now < $p->getExpires()))
                $maxPersonal+=$p->getNumber();
        }

        if($currentPersonal >= $maxPersonal)
            return json_encode(array("success" => false, "error" => "Cannot add personal borad."));
        else
            return json_encode(array("success" => true, "available" => $maxPersonal - $currentPersonal));

    }

    public function __construct(EntityManager $entityManager, SecurityContext $securityContext, ContainerInterface $container)
    {
        $this->em = $entityManager;
        $this->sc = $securityContext;
        $this->container = $container;
    }
}

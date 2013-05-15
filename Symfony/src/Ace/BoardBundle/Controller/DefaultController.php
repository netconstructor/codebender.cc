<?php

namespace Ace\BoardBundle\Controller;

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

        $db_boards = $this->em->getRepository('AceBoardBundle:Board')->findAll();

        foreach($db_boards as $key => $board)
        {
            $boards[] = array(
                "name" => $board->getName(),
                "upload" => json_decode($board->getUpload(), true),
                "bootloader" => json_decode($board->getBootloader(), true),
                "build" => json_decode($board->getBuild(), true),
                "description" => $board->getDescription()
            );
        }
        return new Response(json_encode($boards));
    }

    public function __construct(EntityManager $entityManager, SecurityContext $securityContext, ContainerInterface $container)
    {
        $this->em = $entityManager;
        $this->sc = $securityContext;
        $this->container = $container;
    }
}

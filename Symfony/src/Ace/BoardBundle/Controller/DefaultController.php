<?php

namespace Ace\BoardBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{

    protected $em;

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

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
}

<?php

namespace Ace\ProjectBundle\Tests\Controller;

use Ace\ProjectBundle\Controller\ProjectController;
use Symfony\Component\HttpFoundation\Response;


class ProjectControllerTest extends \PHPUnit_Framework_TestCase
{

    protected $project;


    protected function setUp()
    {
        $this->project = $this->getMockBuilder('Ace\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function setUpController(&$em, &$fc, &$security, $m)
    {
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $security = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContext')
            ->disableOriginalConstructor()
            ->getMock();

        $fc = $this->getMockBuilder('Ace\ProjectBundle\Controller\FilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $controller = $this->getMock('Ace\ProjectBundle\Controller\ProjectController', $methods = $m, $arguments = array($em, $fc, $security));
        return $controller;
    }

}



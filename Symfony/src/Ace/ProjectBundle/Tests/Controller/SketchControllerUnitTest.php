<?php

namespace Ace\ProjectBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

class SketchControllerUnitTest extends \PHPUnit_Framework_TestCase
{
    protected $project;

    //useful functions


    //---createprojectAction
    public function testCreateprojectAction_CanCreatePrivate()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject","createFileAction", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(false))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo("projectName.ino"),$this->equalTo("code"))->will($this->returnValue(new Response('{"success":true}')));
        $response = $controller->createprojectAction(1,"projectName", "code", false);
        $this->assertEquals($response->getContent(), '{"success":true,"id":1}');
    }

    public function testCreateprojectAction_CannotCreatePrivateFromParent()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"error":"Cannot create private project."}'));


        $response = $controller->createprojectAction(1,"projectName", "", false);
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Cannot create private project."}');
    }

    public function testCreateprojectAction_CannotCreatePrivateFile()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject","createFileAction", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(false))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo("projectName.ino"),$this->equalTo("code"))->will($this->returnValue(new Response('{"success":false,"id":1,"filename":"projectName.ino","error":"This file already exists"}')));
        $response = $controller->createprojectAction(1,"projectName", "code", false);
        $this->assertEquals($response->getContent(), '{"success":false,"id":1,"filename":"projectName.ino","error":"This file already exists"}');
    }

    public function testCreateprojectAction_CanCreatePublic()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction", "createFileAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo("projectName.ino"),$this->equalTo("code"))->will($this->returnValue(new Response('{"success":true}')));

        $response = $controller->createprojectAction(1,"projectName", "code", true);
        $this->assertEquals($response->getContent(), '{"success":true,"id":1}');
    }

    public function testCreateprojectAction_CannotCreatePublicFromParent()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":false,"owner_id":1,"name":"projectName"}')));

        $response = $controller->createprojectAction(1,"projectName", "", true);
        $this->assertEquals($response->getContent(), '{"success":false,"owner_id":1,"name":"projectName"}');
    }

    public function testCreateprojectAction_CannotCreatePublicFile()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction", "createFileAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $controller->expects($this->once())->method('createFileAction')->with($this->equalTo(1), $this->equalTo("projectName.ino"),$this->equalTo("code"))->will($this->returnValue(new Response('{"success":false,"id":1,"filename":"projectName.ino","error":"This file already exists"}')));

        $response = $controller->createprojectAction(1,"projectName", "code", true);
        $this->assertEquals($response->getContent(), '{"success":false,"id":1,"filename":"projectName.ino","error":"This file already exists"}');
    }


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

        $mfc = $this->getMockBuilder('Ace\ProjectBundle\Controller\MongoFilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $ffc = $this->getMockBuilder('Ace\ProjectBundle\Controller\DiskFilesController')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();


        $controller = $this->getMock('Ace\ProjectBundle\Controller\SketchController', $methods = $m, $arguments = array($em, $mfc, $ffc, $security, 'disk'));
        return $controller;
    }
}

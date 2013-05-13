<?php

namespace Ace\ProjectBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;
use Ace\ProjectBundle\Controller\SketchController;

class SketchControllerPrivateTester extends SketchController
{
    public function call_canCreateFile($id, $filename)
    {
        return $this->canCreateFile($id, $filename);
    }

    public function call_inoExists($owner, $name)
    {
        return $this->inoExists($owner,$name);
    }
}

class SketchControllerUnitTest extends \PHPUnit_Framework_TestCase
{
    protected $project;

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

    //---cloneAction
    public function testCloneAction_Yes()
    {
        $new_project = $this->getMockBuilder('Ace\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->setMethods(array('setParent'))
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById","nameExists","createAction","listFilesAction", "createFileAction"));
        $controller->expects($this->at(0))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getName')->will($this->returnValue("name"));
        $controller->expects($this->at(1))->method('nameExists')->with($this->equalTo(1), $this->equalTo('name'))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->at(2))->method('nameExists')->with($this->equalTo(1), $this->equalTo('name copy'))->will($this->returnValue('{"success":false}'));

        $this->project->expects($this->once())->method('getDescription')->will($this->returnValue('des'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1), $this->equalTo('name copy'), $this->equalTo('des'), $this->equalTo(true))->will($this->returnValue(new Response( '{"success":true,"id":2}')));

        $controller->expects($this->at(4))->method('getProjectById')->with($this->equalTo(2))->will($this->returnValue($new_project));
        $this->project->expects($this->exactly(2))->method('getId')->will($this->returnValue(1));

        $new_project->expects($this->once())->method('setParent')->with($this->equalTo(1));
        $em->expects($this->once())->method('persist')->with($this->equalTo($new_project));
        $em->expects($this->once())->method('flush');

        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"name.ino","code":"void setup(){}void loop(){}"},{"filename":"header.h","code":"function f(){}"}]}')));

        $controller->expects($this->at(6))->method('createFileAction')->with($this->equalTo(2), $this->equalTo("name copy.ino"), $this->equalTo("void setup(){}void loop(){}"));
        $controller->expects($this->at(7))->method('createFileAction')->with($this->equalTo(2), $this->equalTo("header.h"), $this->equalTo("function f(){}"));
        $response = $controller->cloneAction(1,1);
        $this->assertEquals($response->getContent(), '{"success":true,"id":2}');

    }

    public function testCloneAction_No()
    {
        $new_project = $this->getMockBuilder('Ace\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->setMethods(array('setParent'))
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById","nameExists","createAction","listFilesAction"));
        $controller->expects($this->at(0))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getName')->will($this->returnValue("name"));
        $controller->expects($this->at(1))->method('nameExists')->with($this->equalTo(1), $this->equalTo('name'))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->at(2))->method('nameExists')->with($this->equalTo(1), $this->equalTo('name copy'))->will($this->returnValue('{"success":false}'));

        $this->project->expects($this->once())->method('getDescription')->will($this->returnValue('des'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1), $this->equalTo('name copy'), $this->equalTo('des'), $this->equalTo(true))->will($this->returnValue(new Response( '{"success":false,"owner_id":1,"name":"name copy"}')));

        $response = $controller->cloneAction(1,1);
        $this->assertEquals($response->getContent(),'{"success":false,"id":1}');
    }

    public function testCanCreateFile_YesIno()
    {
        $controller = $this->setUpTesterController($em, $fc, $security, array("inoExists"));

        $controller->expects($this->once())->method('inoExists')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"error":".ino file does not exists"}'));
        $response = $controller->call_canCreateFile(1, 'filename.ino');
        $this->assertEquals($response, '{"success":true}');
    }

    public function testCanCreateFile_YesNotIno()
    {
        $controller = $this->setUpTesterController($em, $fc, $security, array("inoExists"));
        $response = $controller->call_canCreateFile(1, 'filename.h');
        $this->assertEquals($response, '{"success":true}');
    }

    public function testCanCreateFile_No()
    {
        $controller = $this->setUpTesterController($em, $fc, $security, array("inoExists"));

        $controller->expects($this->once())->method('inoExists')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));
        $response = $controller->call_canCreateFile(1, 'filename.ino');
        $this->assertEquals($response, '{"success":false,"id":1,"filename":"filename.ino","error":"Cannot create second .ino file in the same project"}');
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

    private function setUpTesterController(&$em, &$fc, &$security, $m)
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


        $controller = $this->getMock('Ace\ProjectBundle\Tests\Controller\SketchControllerPrivateTester', $methods = $m, $arguments = array($em, $mfc, $ffc, $security, 'disk'));
        return $controller;
    }
}

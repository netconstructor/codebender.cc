<?php

namespace Ace\ProjectBundle\Tests\Controller;

use Ace\ProjectBundle\Controller\ProjectController;
use Symfony\Component\HttpFoundation\Response;


class ProjectControllerTest extends \PHPUnit_Framework_TestCase
{

    protected $project;

    //---createprojectAction
    public function testCreateprojectAction_CanCreatePrivate()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(false))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $response = $controller->createprojectAction(1,"projectName", "", false);
        $this->assertEquals($response->getContent(), '{"success":true,"id":1}');
    }

    public function testCreateprojectAction_CannotCreatePrivate()
    {
        $controller = $this->setUpController($em, $fc, $security, array("canCreatePrivateProject", "createAction"));
        $controller->expects($this->once())->method('canCreatePrivateProject')->with($this->equalTo(1))->will($this->returnValue('{"success":false,"error":"Cannot create private project."}'));


        $response = $controller->createprojectAction(1,"projectName", "", false);
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Cannot create private project."}');
    }

    public function testCreateprojectAction_CanCreatePublic()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":true,"id":1}')));

        $response = $controller->createprojectAction(1,"projectName", "", true);
        $this->assertEquals($response->getContent(), '{"success":true,"id":1}');
    }

    public function testCreateprojectAction_CannotCreatePublic()
    {
        $controller = $this->setUpController($em, $fc, $security, array("createAction"));
        $controller->expects($this->once())->method('createAction')->with($this->equalTo(1),$this->equalTo("projectName"),$this->equalTo(""),$this->equalTo(true))->will($this->returnValue( new Response('{"success":false,"owner_id":1,"name":"projectName"}')));

        $response = $controller->createprojectAction(1,"projectName", "", true);
        $this->assertEquals($response->getContent(), '{"success":false,"owner_id":1,"name":"projectName"}');
    }

    //---deleteAction
    public function testDeleteAction_CanDelete()
    {
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));

        $controller = $this->setUpController($em, $fc, $security, array("getProjectById"));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $fc->expects($this->once())->method('deleteAction')->with($this->equalTo(1234567890))->will($this->returnValue('{"success":true}'));
        $em->expects($this->once())->method('remove')->with($this->equalTo($this->project ));
        $em->expects($this->once())->method('flush');


        $response = $controller->deleteAction(1);
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testDeleteAction_CannotDelete()
    {


        $this->project->expects($this->exactly(2))->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $controller = $this->setUpController($em, $fc, $security, array("getProjectById"));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $fc->expects($this->once())->method('deleteAction')->with($this->equalTo(1234567890))->will($this->returnValue('{"success":false,"error":"No projectfiles found with id: 1234567890"}'));

        $response = $controller->deleteAction(1);
        $this->assertEquals($response->getContent(), '{"success":false,"id":1234567890}');
    }
    //---renameAction
    public function testRenameAction_validName()
    {
        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid", "getProjectById", "listFilesAction"));

        $controller->expects($this->once())->method('nameIsValid')->with($this->equalTo('valid name'))->will($this->returnValue('{"success":true}'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getId')->will($this->returnValue(1));
        $controller->expects($this->once())->method('listFilesAction')->with($this->equalTo(1))->will($this->returnValue(new Response('{"success":true,"list":[{"filename":"private.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}]}')));

        $response = $controller->renameAction(1, 'valid name');
        $this->assertEquals($response->getContent(), '{"success":true,"list":[{"filename":"private.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}]}');
    }

    public function testRenameAction_invalidName()
    {
        $controller = $this->setUpController($em, $fc, $security, array("nameIsValid"));

        $controller->expects($this->once())->method('nameIsValid')->with($this->equalTo('invalid/name'))->will($this->returnValue('{"success":false,"error":"Invalid Name. Please enter a new one."}'));

        $response = $controller->renameAction(1, 'invalid/name');
        $this->assertEquals($response->getContent(), '{"success":false,"error":"Invalid Name. Please enter a new one."}');
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

        $controller = $this->getMock('Ace\ProjectBundle\Controller\ProjectController', $methods = $m, $arguments = array($em, $fc, $security));
        return $controller;
    }

}



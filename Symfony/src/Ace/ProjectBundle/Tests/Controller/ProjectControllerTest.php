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

    //---getNameAction
    public function testGetNameAction_Exists()
    {

        $this->project->expects($this->once())->method('getName')->will($this->returnValue("projectName"));
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $response = $controller->getNameAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"response":"projectName"}');

    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */

    public function testGetNameAction_DoesNotExist()
    {
        $project = NULL;

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($project));

        $controller = $this->setUpController($em, $fc, $security, NULL);
        $em->expects($this->once())->method('getRepository')->with($this->equalTo('AceProjectBundle:Project'))->will($this->returnValue($repo));

        $controller->getNameAction(1);

    }
    //---getParentAction
    public function testGetParentAction_DoesNotExist()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getParent')->will($this->returnValue(NULL));
        $response = $controller->getParentAction(1);
        $this->assertEquals($response->getContent(), '{"success":false}');

    }

    public function testGetParentAction_Exists()
    {

        $parent = $this->getMockBuilder('Ace\ProjectBundle\Entity\Project')
            ->disableOriginalConstructor()
            ->getMock();
        $user = $this->getMockBuilder('Ace\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->at(0))->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getParent')->will($this->returnValue(2));
        $controller->expects($this->at(1))->method('getProjectById')->with($this->equalTo(2))->will($this->returnValue($parent));

        $parent->expects($this->once())->method('getId')->will($this->returnValue(2));
        $this->project->expects($this->once())->method('getOwner')->will($this->returnValue($user));
        $user->expects($this->once())->method('getUsername')->will($this->returnValue('mthrfck'));
        $this->project->expects($this->once())->method('getName')->will($this->returnValue('projectName'));
        $response = $controller->getParentAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"response":{"id":2,"owner":"mthrfck","name":"projectName"}}');
    }

    //---getOwnerAction
    public function testGetOwnerAction()
    {
        $user = $this->getMockBuilder('Ace\UserBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();



        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getOwner')->will($this->returnValue($user));
        $user->expects($this->once())->method('getId')->will($this->returnValue('1'));
        $user->expects($this->once())->method('getUsername')->will($this->returnValue('mthrfck'));
        $user->expects($this->once())->method('getFirstname')->will($this->returnValue('John'));
        $user->expects($this->once())->method('getLastname')->will($this->returnValue('Doe'));
        $response = $controller->getOwnerAction(1);
        $this->assertEquals($response->getContent(), '{"success":true,"response":{"id":"1","username":"mthrfck","firstname":"John","lastname":"Doe"}}');
    }
    //---getDescriptionAction
    public function testGetDescriptionAction()
    {

        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getDescription')->will($this->returnValue("description"));
        $respone = $controller->getDescriptionAction(1);
        $this->assertEquals($respone->getContent(), '{"success":true,"response":"description"}');

    }
    //---setDescriptionAction
    public function testSetDescriptionAction()
    {

        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('setDescription')->with($this->equalTo("newDescription"));

        $em->expects($this->once())->method('persist')->with($this->equalTo($this->project ));
        $em->expects($this->once())->method('flush');

        $respone = $controller->setDescriptionAction(1, 'newDescription');
        $this->assertEquals($respone->getContent(), '{"success":true}');

    }
    //---listFilesAction
    public function testListFilesAction_HasPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkProjectPermissions'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $controller->expects($this->once())->method('checkProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":true}'));

        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));

        $fc->expects($this->once())->method('listFilesAction')->with($this->equalTo(1234567890))->will($this->returnValue('{"success":true,"list":[{"filename":"private.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}]}'));

        $response = $controller->listFilesAction(1);

        $this->assertEquals($response->getContent(), '{"success":true,"list":[{"filename":"private.ino","code":"void setup()\n{\n\t\n}\n\nvoid loop()\n{\n\t\n}\n"}]}');

    }

    public function testListFilesAction_HasNoPermissions()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'checkProjectPermissions'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $controller->expects($this->once())->method('checkProjectPermissions')->with($this->equalTo(1))->will($this->returnValue('{"success":false}'));


        $response = $controller->listFilesAction(1);

        $this->assertEquals($response->getContent(), '{"success":false}');

    }

    //---createFileAction
    public function testCreateFileAction_canCreate()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'canCreateFile'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $this->project->expects($this->once())->method('getId')->will($this->returnValue(1));

        $controller->expects($this->once())->method('canCreateFile')->with($this->equalTo(1), $this->equalTo('filename'))->will($this->returnValue('{"success":true}'));

        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));

        $fc->expects($this->once())->method('createFileAction')->with($this->equalTo(1234567890), $this->equalTo('filename'), $this->equalTo('void setup(){}'))->will($this->returnValue('{"success":true}'));

        $response = $controller->createFileAction(1, 'filename', 'void setup(){}');

        $this->assertEquals($response->getContent(), '{"success":true}');
    }


    public function testCreateFileAction_cannotCreate()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById', 'canCreateFile'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $this->project->expects($this->once())->method('getId')->will($this->returnValue(1));

        $controller->expects($this->once())->method('canCreateFile')->with($this->equalTo(1), $this->equalTo('filename'))->will($this->returnValue('{"success":false}'));

        $response = $controller->createFileAction(1, 'filename', 'void setup(){}');

        $this->assertEquals($response->getContent(), '{"success":false}');
    }
    //---getFileAction
    public function testGetFileAction_canGet()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('getFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'))->will($this->returnValue('{"success":false}'));
        $response = $controller->getFileAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    public function testGetFileAction_cannotGet()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('getFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'))->will($this->returnValue('{"success":true,"code":"void setup(){}"}'));
        $response = $controller->getFileAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":true,"code":"void setup(){}"}');
    }

    //---setFileAction
    public function testSetFileAction_canSet()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('setFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'),$this->equalTo('void setup(){}'))->will($this->returnValue('{"success":true}'));
        $response = $controller->setFileAction(1, 'name', 'void setup(){}');
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testSetFileAction_cannotSet()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('setFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'),$this->equalTo('void setup(){}'))->will($this->returnValue('{"success":false}'));
        $response = $controller->setFileAction(1, 'name', 'void setup(){}');
        $this->assertEquals($response->getContent(), '{"success":false}');
    }

    //---deleteFileAction
    public function testDeleteFileAction_canDelete()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('deleteFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'))->will($this->returnValue('{"success":true}'));
        $response = $controller->deleteFileAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testDeleteFileAction_cannotDelete()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('deleteFileAction')->with($this->equalTo(1234567890),$this->equalTo('name'))->will($this->returnValue('{"success":false,"filename":"name","error":"File name does not exist}'));
        $response = $controller->deleteFileAction(1, 'name');
        $this->assertEquals($response->getContent(), '{"success":false,"filename":"name","error":"File name does not exist}');
    }
    //---renameFileAction
    public function testRenameFileAction_canRename()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('renameFileAction')->with($this->equalTo(1234567890),$this->equalTo('old'),$this->equalTo('new'))->will($this->returnValue('{"success":true}'));
        $response = $controller->renameFileAction(1, 'old', 'new');
        $this->assertEquals($response->getContent(), '{"success":true}');
    }

    public function testRenameFileAction_cannotRename()
    {
        $controller = $this->setUpController($em, $fc, $security, array('getProjectById'));

        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1))->will($this->returnValue($this->project));
        $this->project->expects($this->once())->method('getProjectfilesId')->will($this->returnValue(1234567890));
        $fc->expects($this->once())->method('renameFileAction')->with($this->equalTo(1234567890),$this->equalTo('old'),$this->equalTo('new'))->will($this->returnValue('{"success":false,"filename":"old","error":"File old does not exist}'));
        $response = $controller->renameFileAction(1, 'old', 'new');
        $this->assertEquals($response->getContent(), '{"success":false,"filename":"old","error":"File old does not exist}');
    }
    //---checkExistsAction
    public function testCheckExistsAction_Exists()
    {

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->exactly(1))->method('getRepository')->with($this->equalTo('AceProjectBundle:Project'))->will($this->returnValue($repo));

        $response = $controller->checkExistsAction(1);
        $this->assertEquals($response->getContent(), json_encode(array("success" => true)));

    }

    public function testCheckExistsAction_DoesNotExist()
    {
        $project = NULL;

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($project));

        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('AceProjectBundle:Project'))->will($this->returnValue($repo));

        $response = $controller->checkExistsAction(1);
        $this->assertEquals($response->getContent(), json_encode(array("success" => false)));

    }
    //---getProjectById
    public function testGetProjectById_Exists()
    {

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($this->project));

        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('AceProjectBundle:Project'))->will($this->returnValue($repo));

        $response = $controller->getProjectById(1);
        $this->assertEquals($response, $this->project);


    }

    /**
     * @expectedException Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGetProjectById_DoesNotExist()
    {
        $project = NULL;

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array("find"))
            ->getMock();

        $repo->expects($this->once())->method('find')->with($this->equalTo(1))->will($this->returnValue($project));

        $controller = $this->setUpController($em, $fc, $security, NULL);

        $em->expects($this->once())->method('getRepository')->with($this->equalTo('AceProjectBundle:Project'))->will($this->returnValue($repo));

        $controller->getProjectById(1);

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



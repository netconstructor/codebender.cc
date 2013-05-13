<?php

namespace Ace\ProjectBundle\Tests\Controller;

use Ace\ProjectBundle\Controller\MongoFilesController;

class MongoFilesControllerTester extends MongoFilesController
{
    public function call_listFiles($id)
    {
       return $this->listFiles($id);
    }
}
class MongoFilesControllerUnitTest extends \PHPUnit_Framework_TestCase
{
    protected $pf;
	public function testCreateAction()
    {
        $controller = $this->setUpController($dm,NULL);
        $dm->expects($this->once())->method("persist");
        $dm->expects($this->once())->method('flush');

        $response = $controller->createAction();
        $this->assertEquals($response, '{"success":true,"id":null}');

    }

    public function testDeleteAction()
    {
        $controller = $this->setUpController($dm,array('getProjectById'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1234))->will($this->returnValue($this->pf));
        $dm->expects($this->once())->method("remove")->with($this->equalTo($this->pf));
        $dm->expects($this->once())->method('flush');

        $response = $controller->deleteAction(1234);
        $this->assertEquals($response, '{"success":true}');

    }

    public function testListFilesAction()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");
        $controller = $this->setUpController($dm,array('listFiles'));
        $controller->expects($this->once())->method('listFiles')->with($this->equalTo(1234))->will($this->returnValue($list));
        $response = $controller->listFilesAction(1234);
        $this->assertEquals($response, '{"success":true,"list":[{"filename":"project.ino","code":"void setup(){}"},{"filename":"header.h","code":"void function(){}"}]}'
        );
    }

    public function testCreateFileAction_Yes()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");

        $controller = $this->setUpController($dm, array('listFiles', 'canCreateFile', 'setFilesById'));
        $controller->expects($this->once())->method('listFiles')->with($this->equalTo(1234))->will($this->returnValue($list));
        $list[] = array("filename" => "header2.h", "code" => "void function2(){}");
        $controller->expects($this->once())->method('canCreateFile')->with($this->equalTo(1234), $this->equalTo('header2.h'))->will($this->returnValue('{"success":true}'));
        $controller->expects($this->once())->method('setFilesById')->with($this->equalTo(1234), $this->equalTo($list));
        $response = $controller->createFileAction(1234, 'header2.h', 'void function2(){}');
        $this->assertEquals($response, '{"success":true}'
        );

    }

    public function testCreateFileAction_No()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");

        $controller = $this->setUpController($dm, array('listFiles', 'canCreateFile'));
        $controller->expects($this->once())->method('listFiles')->with($this->equalTo(1234))->will($this->returnValue($list));

        $controller->expects($this->once())->method('canCreateFile')->with($this->equalTo(1234), $this->equalTo('header.h'))->will($this->returnValue('{"success":false,"id":1,"filename":"header1.h","error":"This file already exists"}'));

        $response = $controller->createFileAction(1234, 'header.h', 'void function2(){}');
        $this->assertEquals($response, '{"success":false,"id":1,"filename":"header1.h","error":"This file already exists"}'

        );

    }

    public function testGetFileAction_Yes()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");

        $controller = $this->setUpController($dm, array('listFiles'));
        $controller->expects($this->once())->method('listFiles')->with($this->equalTo(1234))->will($this->returnValue($list));

        $response = $controller->getFileAction(1234, 'header.h');
        $this->assertEquals($response, '{"success":true,"code":"void function(){}"}'

        );
    }

    public function testGetFileAction_No()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");

        $controller = $this->setUpController($dm, array('listFiles'));
        $controller->expects($this->once())->method('listFiles')->with($this->equalTo(1234))->will($this->returnValue($list));

        $response = $controller->getFileAction(1234, 'header2.h');
        $this->assertEquals($response, '{"success":false}'

        );
    }

    public function testSetFileAction_Yes()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");

        $controller = $this->setUpController($dm, array('listFiles', 'setFilesById'));
        $controller->expects($this->once())->method('listFiles')->with($this->equalTo(1234))->will($this->returnValue($list));
        $list[1]['code'] =  "void function(int x){}";
        $controller->expects($this->once())->method('setFilesById')->with($this->equalTo(1234), $this->equalTo($list));

        $response = $controller->setFileAction(1234, 'header.h', "void function(int x){}");
        $this->assertEquals($response, '{"success":true}');
    }

    public function testSetFileAction_No()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");

        $controller = $this->setUpController($dm, array('listFiles', 'setFilesById'));
        $controller->expects($this->once())->method('listFiles')->with($this->equalTo(1234))->will($this->returnValue($list));

        $response = $controller->setFileAction(1234, 'header2.h', "void function(int x){}");
        $this->assertEquals($response, '{"success":false}');
    }

    public function testSetFilesById()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");

        $controller = $this->setUpController($dm, array('getProjectById'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1234))->will($this->returnValue($this->pf));
        $this->pf->expects($this->once())->method('setFiles')->with($this->equalTo($list));
        $this->pf->expects($this->once())->method('setFilesTimestamp');
        $dm->expects($this->once())->method('persist')->with($this->equalTo($this->pf));
        $dm->expects($this->once())->method('flush');

        $controller->setFilesById(1234, $list);


    }

    public function testListFiles()
    {
        $list = array();
        $list[] = array("filename" => "project.ino", "code" => "void setup(){}");
        $list[] = array("filename" => "header.h", "code" => "void function(){}");

        $controller = $this->setUpTesterController($dm, array('getProjectById'));
        $controller->expects($this->once())->method('getProjectById')->with($this->equalTo(1234))->will($this->returnValue($this->pf));
        $this->pf->expects($this->once())->method('getFiles')->will($this->returnValue($list));

        $response = $controller->call_listFiles(1234);
        $this->assertEquals($response, $list);

    }
    protected function setUp()
    {
        $this->pf = $this->getMockBuilder('Ace\ProjectBundle\Document\ProjectFiles')
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function setUpController(&$dm, $m)
    {
        $dm = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->getMock('Ace\ProjectBundle\Controller\MongoFilesController', $methods = $m, $arguments = array($dm));
        return $controller;
    }

    private function setUpTesterController(&$dm, $m)
    {
        $dm = $this->getMockBuilder('Doctrine\ODM\MongoDB\DocumentManager')
            ->disableOriginalConstructor()
            ->getMock();

        $controller = $this->getMock('Ace\ProjectBundle\Tests\Controller\MongoFilesControllerTester', $methods = $m, $arguments = array($dm));
        return $controller;
    }
}

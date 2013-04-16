<?php
// src/Ace/ProjectBundle/Controller/DiskFilesController.php

namespace Ace\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DiskFilesController extends FilesController
{
    protected $dir;

    public function createAction()
    {
        $projects = scandir($this->dir);
        do
        {
            $id = uniqid($more_entropy=true);
        } while(in_array($id, $projects));
        mkdir($this->dir.$id);
        mkdir($this->dir.$id."/files");
        return json_encode(array("success" => true, "id" => $id));
    }

    public function deleteAction($id)
    {
        $dir = $this->dir.$id;
        if($this->deleteDirectory($dir))
            return json_encode(array("success" => true));
        else
            return json_encode(array("success" => false, "error" => "No projectfiles found with id: ".$id));
    }

    public function listFilesAction($id)
    {
        $list = $this->listFiles($id);
        return json_encode(array("success" => true, "list" => $list));
    }

    public function createFileAction($id, $filename, $code)
    {

        $canCreateFile = json_decode($this->canCreateFile($id, $filename), true);
        if(!$canCreateFile["success"])
            return json_encode($canCreateFile);
        $dir = $this->dir."/".$id."/files";
        file_put_contents($dir."/".$filename,$code);

        return json_encode(array("success" => true));
    }

    public function getFileAction($id, $filename)
    {
        $response = array("success" => false);
        $list = $this->listFiles($id);
        foreach($list as $file)
        {
            if($file["filename"] == $filename)
                $response=array("success" => true, "code" => $file["code"]);
        }
        return json_encode($response);
    }

    public function setFileAction($id, $filename, $code)
    {
        $dir = $this->dir.$id."/files/";
        if($this->fileExists($id,$dir.$filename))
        {
            file_put_contents($dir.$filename,$code);
            return json_encode(array("success" => true));
        }
        return json_encode(array("success" => false));

    }

    public function deleteFileAction($id, $filename)
    {
        $fileExists = json_decode($this->fileExists($id, $filename), true);
        if(!$fileExists["success"])
            return json_encode($fileExists);
        $dir = $this->dir.$id."/files/";
        unlink($dir.$filename);
        return json_encode(array("success" => true));
    }

    public function renameFileAction($id, $filename, $new_filename)
    {
        $fileExists = json_decode($this->fileExists($id, $filename), true);
        if(!$fileExists["success"])
            return json_encode($fileExists);

        $canCreateFile = json_decode($this->canCreateFile($id, $new_filename), true);
        if($canCreateFile["success"])
        {
            $dir = $this->dir.$id."/files/";
            rename($dir.$filename, $dir.$new_filename);
        }
        return json_encode($canCreateFile);
    }


    protected function listFiles($id)
    {
        $dir = $this->dir.$id."/files/";
        $list = array();
        $objects = scandir($dir);
        foreach ($objects as $object)
        {
            if(!is_dir($dir.$object))
            {
                $file["filename"] = $object;
                $file["code"] = file_get_contents($dir.$object);
                $list[] = $file;
            }
        }
        return $list;
    }


    private function deleteDirectory($dir)
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);
            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (filetype($dir."/".$object) == "dir") $this->deleteDirectory($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
            return true;
        }
        else return false;
    }

    public function __construct($directory)
    {
        if(!(substr_compare($directory, '/', 1, 1) === 0))
            $directory = $directory.'/';
        $this->dir = $directory;
    }
}


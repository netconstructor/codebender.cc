<?php

namespace Ace\GenericBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditorController extends Controller
{		
	public function editAction($id)
	{
		if (!$this->get('security.context')->isGranted('ROLE_USER'))
		{
			return $this->redirect($this->generateUrl('AceGenericBundle_project', array("id"=> $id)));
		}

		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

		if (!$user)
		{
			throw $this->createNotFoundException('No user found with id '.$name);
		}

		$projectmanager = $this->get('projectmanager');
		$projects = $projectmanager->listAction($user->getID())->getContent();
		$projects = json_decode($projects, true);
		$exists = false;
		foreach($projects as $project)
		{
			if ($project["id"] == $id)
				$exists = true;
		}

		$files = $projectmanager->listFilesAction($id)->getContent();
		$files = json_decode($files, true);
		foreach($files as $key=>$file)
		{
			$files[$key]["code"] = htmlspecialchars($file["code"]);
		}

		$utilities = $this->get('utilities');

		$examples = json_decode($utilities->get_data($this->container->getParameter('library'), 'data', "builtin"), true);
		$lib_examples = json_decode($utilities->get_data($this->container->getParameter('library'), 'data', "included"), true);
		$extra_lib_examples = json_decode($utilities->get_data($this->container->getParameter('library'), 'data', "external"), true);

		$examples = $examples["list"];
		$lib_examples = $lib_examples["list"];
		$extra_lib_examples = $extra_lib_examples["list"];

		// die(var_dump($examples)." ".var_dump($lib_examples)." ".var_dump($extra_lib_examples)." ");

		return $this->render('AceGenericBundle:Editor:editor.html.twig', array('username'=>$name, 'project_id' => $id, 'examples' => $examples, 'lib_examples' => $lib_examples,'extra_lib_examples' => $extra_lib_examples, 'files' => $files));
	}		
}

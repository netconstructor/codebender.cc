<?php

namespace Ace\GenericBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditorController extends Controller
{		
	public function editAction($project_name)
	{
		$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
		$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);

		$hex_exists = false;

		$resp = $this->forward('AceFileBundle:Default:getTimestamp', array('project_name' => $project_name, 'type' => "code"));
		$codeTimestamp = $resp->getContent();

		$resp = $this->forward('AceFileBundle:Default:getTimestamp', array('project_name' => $project_name, 'type' => "hex"));
		$hexTimestamp = $resp->getContent();
		if($hexTimestamp > $codeTimestamp)
			$hex_exists = true;

		$utilities = $this->get('utilities');
		$examples = json_decode($utilities->get_data($this->container->getParameter('library'), 'data', "builtin"), true);
		$lib_examples = json_decode($utilities->get_data($this->container->getParameter('library'), 'data', "included"), true);
		$extra_lib_examples = json_decode($utilities->get_data($this->container->getParameter('library'), 'data', "external"), true);

		$examples = $examples["list"];
		$lib_examples = $lib_examples["list"];
		$extra_lib_examples = $extra_lib_examples["list"];

		// die(var_dump($examples)." ".var_dump($lib_examples)." ".var_dump($extra_lib_examples)." ");

		return $this->render('AceGenericBundle:Editor:editor.html.twig', array('username'=>$name, 'project_name' => $project_name, 'examples' => $examples, 'lib_examples' => $lib_examples,'extra_lib_examples' => $extra_lib_examples, 'hex_exists' => $hex_exists));
	}		
}

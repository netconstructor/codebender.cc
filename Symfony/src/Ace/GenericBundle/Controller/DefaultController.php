<?php

namespace Ace\GenericBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
	public function librariesAction()
	{
		$utilities = $this->get('utilities');
		$libraries = json_decode($utilities->get_data($this->container->getParameter('library'), 'data', "external"), true);
		$libraries = $libraries["list"];
		
		foreach($libraries as &$libinfo)
		{
			if(isset($libinfo["description"]))
				$libinfo["description"] = $utilities->get($libinfo["description"]["url"]);
		}
		
		
		return $this->render('AceGenericBundle:Default:libraries.html.twig', array('libraries' => $libraries));
	}
	
    
}

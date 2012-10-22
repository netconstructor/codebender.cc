<?php

namespace Ace\SenderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Ace\UtilitiesBundle\Handler\DefaultHandler;


class DefaultController extends Controller
{
	public function tftpAction()
	{
		$response = new Response('404 Not Found!', 404, array('content-type' => 'text/plain'));
		$project_name = $this->getRequest()->request->get('project_name');
		$ip = $this->getRequest()->request->get('ip');
		if($project_name && $ip)
		{
			$resp = $this->forward('AceFileBundle:Default:getMyHex', array('project_name' => $project_name));
			$value = $resp->getContent();

			$data = "ERROR";

			$utilities = new DefaultHandler();			
			$data = $utilities->get_data("http://sender.dev.codebender.cc", 'hex', urlencode($value)."&ip=".$ip);
			$response->setContent($data);
			$response->setStatusCode(200);
			$response->headers->set('Content-Type', 'text/html');
		}
		return $response;
	}

}

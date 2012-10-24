<?php

namespace Ace\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class DefaultController extends Controller
{
    public function indexAction()
    {
        if ($this->getRequest()->getMethod() === 'GET') {
            
            $query = $this->getRequest()->query->get('query');

			$repository = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser');
			$users = $repository->createQueryBuilder('u')
			    ->where('u.username LIKE :name OR u.firstname LIKE :name OR u.lastname LIKE :name OR u.twitter LIKE :name')
				->setParameter('name', "%".$query."%")->getQuery()->getResult();

			$projectmanager = $this->get('projectmanager');
			$files = json_decode($projectmanager->searchAction($query)->getContent(), true);

	        return $this->render('AceSearchBundle:Default:index.html.twig', array('query'=>$query, 'users'=>$users, 'files'=>$files));
        }
    }
}

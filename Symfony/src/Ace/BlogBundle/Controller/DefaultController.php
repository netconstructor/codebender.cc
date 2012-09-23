<?php

namespace Ace\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
   public function blogAction($arg)
	{
		// $posts = $this->getDoctrine()->getRepository('AceMiscBundle:BlogPost')->findAll();

		$em = $this->getDoctrine()->getEntityManager();
		$qb = $em->createQueryBuilder();

		$qb->add('select', 'u')->add('from', 'AceBlogBundle:BlogPost u')->add('orderBy', 'u.date DESC');
		$posts = $qb->getQuery()->getResult();
		
		if($arg == 'html')
			return $this->render('AceBlogBundle:Default:blog.html.twig', array("posts" => $posts));
		else if ($arg == 'rss' )
		{
			$response = $this->render('AceBlogBundle:Default:blog_rss.html.twig', array("posts" => $posts));
			$response->headers->set('Content-Type', 'application/rss+xml');
			return $response;
		}
		else if($arg == 'new')
		{
			if (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
			{
				throw new AccessDeniedException();
			}
			else
			{
				$title = $this->getRequest()->query->get('title');
				$text = $this->getRequest()->query->get('msgpost');
				$author = $this->container->get('security.context')->getToken()->getUser()->getUsername();
				$em = $this->getDoctrine()->getEntityManager();
				$post = new BlogPost();
				$post->setTitle($title);
				$post->setText($text);
				$post->setAuthor($author);
				$post->setDate(new \DateTime("now"));
				$em->persist($post);
				$em->flush();
				return $this->redirect($this->generateUrl('AceBlogBundle_blog'));
			}
			
		}
	}

	public function blog_newAction()
	{
		if (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
		{
			throw new AccessDeniedException();
		}
		else
		{
			$title = $this->getRequest()->query->get('title');
			$text = $this->getRequest()->query->get('msgpost');
			$author = $this->container->get('security.context')->getToken()->getUser()->getUsername();
			$em = $this->getDoctrine()->getEntityManager();
			$post = new BlogPost();
			$post->setTitle($title);
			$post->setText($text);
			$post->setAuthor($author);
			$post->setDate(new \DateTime("now"));
			$em->persist($post);
			$em->flush();
			return $this->redirect($this->generateUrl('AceBlogBundle_blog'));
		}
	}
}

<?php

namespace Ace\MiscBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Ace\MiscBundle\Entity\BlogPost;
use Ace\MiscBundle\Entity\Contact;
use Ace\MiscBundle\Entity\Prereg;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class developer
{
	public $name;
	public $image;
	public $description;
	function __construct($name, $subtext, $image, $description)
	{
		$this->name = $name;
		$this->subtext = $subtext;
		$this->image = $image;
		$this->description = $description;
	}
}

class DefaultController extends Controller
{

	public function aboutAction()
	{
		return $this->render('AceMiscBundle:Default:about.html.twig');
	}

	public function teamAction()
	{
		$tzikis_name = "Vasilis Georgitzikis";
		$tzikis_title = "teh lead";
		$tzikis_avatar = "http://www.gravatar.com/avatar/1a6a5289ac4473b5731fa9d9a3032828?s=260";
		$tzikis_desc = "I am a student at the Computer Engineering and Informatics Department of the University of Patras, Greece, a researcher at the Research Academic Computer Technology Institute, and an Arduino and iPhone/OSX/Cocoa developer. Basically, just a geek who likes building stuff, which is what started codebender in the first place.";
		$tzikis = new developer($tzikis_name, $tzikis_title, $tzikis_avatar, $tzikis_desc);

		$tsampas_name = "Stelios Tsampas";
		$tsampas_title = "teh crazor";
		$tsampas_avatar = "http://secure.gravatar.com/avatar/a5eb2b494a07a39ab0eef0d10aa86c84?s=260";
		$tsampas_desc="Yet another student at CEID. My task is to make sure to bring crazy ideas to the table and let others assess their value. I'm also responsible for the Arduino Ethernet TFTP bootloader, the only crazy idea that didn't originate from me. I also have a 'wierd' coding style that causes much distress to $tzikis_name.";
		$tsampas = new developer($tsampas_name, $tsampas_title, $tsampas_avatar, $tsampas_desc);

		$amaxilatis_name = "Dimitris Amaxilatis";
		$amaxilatis_title = "teh code monkey";
		$amaxilatis_avatar = "http://codebender.cc/images/amaxilatis.jpg";
		$amaxilatis_desc = "Master Student at the Computer Engineering and Informatics Department of the University of Patras, Greece. Researcher at  the Research Unit 1 of Computer Technology Institute & Press (Diophantus) in the fields of Distributed Systems and Wireless Sensor Networks.";
		$amaxilatis = new developer($amaxilatis_name, $amaxilatis_title, $amaxilatis_avatar, $amaxilatis_desc);

		$kousta_name = "Maria Kousta";
		$kousta_title = "teh lady";
		$kousta_avatar = "http://codebender.cc/images/kousta.png";
		$kousta_desc = "A CEID graduate. My task is to develop the various parts of the site besides the core 'code and compile' page that make it a truly social-building website.";
		$kousta = new developer($kousta_name, $kousta_title, $kousta_avatar, $kousta_desc);

		$orfanos_name = "Markellos Orfanos";
		$orfanos_title = "teh fireman";
		$orfanos_avatar = "http://codebender.cc/images/orfanos.jpg";
		$orfanos_desc = "I am also (not for long I hope) a student at the Computer Engineering & Informatics Department and probably the most important person in the team. My task? Make sure everyone keeps calm and the team is having fun. And yes, I'm the one who developed our wonderful options page. Apart from that, I'm trying to graduate and some time in the future to become a full blown Gentoo developer.";
		$orfanos = new developer($orfanos_name, $orfanos_title, $orfanos_avatar, $orfanos_desc);

		$developers = array($tzikis, $tsampas, $amaxilatis, $kousta, $orfanos);
		return $this->render('AceMiscBundle:Default:team.html.twig', array("developers" => $developers));
	}
	public function blogAction($arg)
	{
		// $posts = $this->getDoctrine()->getRepository('AceMiscBundle:BlogPost')->findAll();

		$em = $this->getDoctrine()->getEntityManager();
		$qb = $em->createQueryBuilder();

		$qb->add('select', 'u')->add('from', 'AceMiscBundle:BlogPost u')->add('orderBy', 'u.date DESC');
		$posts = $qb->getQuery()->getResult();
		
		if($arg == 'html')
			return $this->render('AceMiscBundle:Default:blog.html.twig', array("posts" => $posts));
		else if ($arg == 'rss' )
			$response = $this->render('AceMiscBundle:Default:blog_rss.html.twig', array("posts" => $posts));
			$response->headers->set('Content-Type', 'application/rss+xml');
			return $response;
			
		
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
			return $this->redirect($this->generateUrl('AceMiscBundle_blog'));
		}
	}			

	public function tutorialsAction()
	{
		return $this->render('AceMiscBundle:Default:tutorials.html.twig');
	}

	public function contactAction(Request $request)
	{	    
        // create a task and give it some dummy data for this example
        $task = new Contact();
		if ($this->get('security.context')->isGranted('ROLE_USER') === true)
		{
			$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
			$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);
	        $task->setName($user->getFirstname()." ".$user->getLastname()." (".$user->getUsername().")");
	        $task->setEmail($user->getEmail());
		}

        $form = $this->createFormBuilder($task)
            ->add('name', 'text')
            ->add('email', 'email')
            ->add('text', 'textarea')
            ->getForm();

		if ($request->getMethod() == 'POST') 
		{
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$email_addr = $this->container->getParameter('email.addr');
				
				// perform some action, such as saving the task to the database
			    $message = \Swift_Message::newInstance()
			        ->setSubject('codebender contact request')
			        ->setFrom($email_addr)
			        ->setTo($email_addr)
			        ->setBody($this->renderView('AceMiscBundle:Default:contact_email_form.txt.twig', array('task' => $task)))
			    ;
			    $this->get('mailer')->send($message);
				$this->get('session')->setFlash('notice', 'Your message was sent!');

				return $this->redirect($this->generateUrl('AceMiscBundle_contact'));
			}
		}

        return $this->render('AceMiscBundle:Default:contact.html.twig', array(
            'form' => $form->createView(),
        ));
	}
	
	public function preregAction(Request $request)
	{	    
        // create a task and give it some dummy data for this example
        $task = new Prereg();
		if ($this->get('security.context')->isGranted('ROLE_USER') === true)
		{
			$name = $this->container->get('security.context')->getToken()->getUser()->getUsername();
			$user = $this->getDoctrine()->getRepository('AceExperimentalUserBundle:ExperimentalUser')->findOneByUsername($name);
	        $task->setName($user->getFirstname()." ".$user->getLastname()." (".$user->getUsername().")");
	        $task->setEmail($user->getEmail());
		}

        $form = $this->createFormBuilder($task)
            ->add('name', 'text')
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('site', 'text')
            ->add('description', 'textarea')
            ->add('reason', 'textarea')
            ->getForm();

		if ($request->getMethod() == 'POST') 
		{
			$form->bindRequest($request);

			if ($form->isValid())
			{
				$email_addr = $this->container->getParameter('email.addr');
				
				// perform some action, such as saving the task to the database
			    $message = \Swift_Message::newInstance()
			        ->setSubject('[codebender][preregistration] Preregistration Request')
			        ->setFrom($task->getEmail())
			        ->setTo($email_addr)
			        ->setBody($this->renderView('AceMiscBundle:Default:prereg_email_form.txt.twig', array('task' => $task)))
			    ;
			    $this->get('mailer')->send($message);
				$this->get('session')->setFlash('notice', 'Your registration request was sent!');

				return $this->redirect($this->generateUrl('AceMiscBundle_prereg'));
			}
		}

        return $this->render('AceMiscBundle:Default:prereg.html.twig', array(
            'form' => $form->createView(),
        ));
	}
	
	public function notifyAction()
	{
		$msg = $this->getRequest()->query->get('message');
		if($msg)
		{
			$email_addr = $this->container->getParameter('email.addr');
			$message = \Swift_Message::newInstance()
		        ->setSubject('[codebender][notification] Java Notification')
		        ->setFrom($email_addr)
		        ->setTo("amaxilatis@codebender.cc")
		        ->setBody($msg);
		    $this->get('mailer')->send($message);
		}
		return new Response("OK");
	}
	
}

<?php

namespace Ace\BoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('AceBoardBundle:Default:index.html.twig', array('name' => $name));
    }
}

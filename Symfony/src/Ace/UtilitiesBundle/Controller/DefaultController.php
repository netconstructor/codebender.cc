<?php

namespace Ace\UtilitiesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('AceUtilitiesBundle:Default:index.html.twig', array('name' => $name));
    }
}

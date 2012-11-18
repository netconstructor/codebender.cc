<?php

namespace Ace\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use FOS\UserBundle\Model\UserInterface;

class RegistrationFormHandler extends BaseHandler
{
    protected function onSuccess(UserInterface $user, $confirmation)
    {

        parent::onSuccess($user, $confirmation);
		
		// Mailchimp Integration
        //$form = $this->form->getData();
        
        /* If newsletter is checked update newsletter mailing list
        if($form['newsletter'])
        {
        	
        }
        */        
    }
}

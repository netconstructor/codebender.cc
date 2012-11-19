<?php

namespace Ace\UserBundle\Form\Handler;

use FOS\UserBundle\Form\Handler\RegistrationFormHandler as BaseHandler;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;
use Ace\UserBundle\Controller\DefaultController as UserController;
use Ace\ProjectBundle\Controller\DefaultController as ProjectManager;

class RegistrationFormHandler extends BaseHandler
{
	private $usercontroller;
	private $projectmanager;

    public function __construct(Form $form, Request $request, UserManagerInterface $userManager, MailerInterface $mailer, UserController $usercontroller, ProjectManager $projectmanager)
    {
		parent::__construct($form, $request, $userManager, $mailer);
		$this->usercontroller = $usercontroller;
		$this->projectmanager = $projectmanager;
    }

    protected function onSuccess(UserInterface $user, $confirmation)
    {

        parent::onSuccess($user, $confirmation);
		
		$first_code =
"/*
	Blink
	Turns on an LED on for one second, then off for one second, repeatedly.

	This example code is in the public domain.
*/

void setup()
{
	// initialize the digital pin as an output.
	// Pin 13 has an LED connected on most Arduino boards:
	pinMode(13, OUTPUT);
}

void loop()
{
	digitalWrite(13, HIGH); // set the LED on
	delay(1000); // wait for a second
	digitalWrite(13, LOW); // set the LED off
	delay(1000); // wait for a second
}
";

		$second_code =
"/*
	Prints an incremental number the serial monitor
*/
int number = 0;

void setup()
{
	Serial.begin(9600);
}

void loop()
{
	Serial.println(number);
	delay(500);
}
";
		//create new projects
		$username = $user->getUsernameCanonical();
		$user = json_decode($this->usercontroller->getUserAction($username)->getContent(), true);
		$response = $this->projectmanager->createprojectAction($user["id"], "First Example", $first_code)->getContent();
		$response = $this->projectmanager->createprojectAction($user["id"], "Second Example", $second_code)->getContent();

		// Mailchimp Integration
        //$form = $this->form->getData();
        
        /* If newsletter is checked update newsletter mailing list
        if($form['newsletter'])
        {
        	
        }
        */        
    }
}

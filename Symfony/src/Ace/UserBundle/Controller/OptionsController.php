<?php

namespace Ace\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Ace\UserBundle\Form\Type\OptionsFormType;
use Ace\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Ace\UserBundle\Validator\Constraints\PasswordConstraint;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use MCAPI;

/**
 * Controller managing the user profile
 */
class OptionsController extends Controller
{
	protected $templating;
	protected $sc;
	protected $container;
	protected $request;
	protected $userManager;
	protected $encoderFactory;
	protected $entityManager;
	protected $listapi;
	protected $listid;
	
    public function optionsEditAction()
    {
		// Get currently logged in user
        $currentUser = $this->sc->getToken()->getUser();
        
        if (!is_object($currentUser) || !$currentUser instanceof User) {
            throw new AccessDeniedException('Sorry, this user does not have access to this section.');
        }
        
        // Get user's avatar
		$image = $this->get('utilities')->get_gravatar($currentUser->getEmail(), 120);
				
        $form = $this->createForm(new OptionsFormType());
        
		$form->get('username')	->setData($currentUser->getUsername());
		$form->get('firstname')	->setData($currentUser->getFirstname());
		$form->get('lastname')	->setData($currentUser->getLastname());
		$form->get('email')		->setData($currentUser->getEmail());
		$form->get('twitter')	->setData($currentUser->getTwitter());
		 
		if ('POST' === $this->request->getMethod()) {
			
		    $form->bindRequest($this->request);
			
			// Check if email is already in database
			$email = $form->get('email')->getData();
            if($email != NULL)
			{
				if($this->get('usercontroller')->emailExistsAction($email)){
					if($email !== $currentUser->getEmail())
						$form->get('email')->addError(new FormError('This email address is already in use by another member'));
				}
			}
			
			// flag to state user password request
			$currentPassword = $form->get('currentPassword')->getData();
			if($currentPassword != NULL){
				$passChange = $this->isCurrentPass($form, $currentPassword);
				if(!$passChange)
					$form->get('currentPassword')->addError(new FormError('Wrong password!'));
			}
			else
				$passChange = false;
				
			if ($form->isValid())
			{
				$this->em->persist($currentUser);
				
				// update user object from form data only if it is changed
				// avoid query unless necessary
				$updated=false;
				$firstname = $form->get('firstname')->getData();
				if($firstname !== $currentUser->getFirstname()){
					$currentUser->setFirstname($firstname);
					$updated = true;
				}
				
				$lastname = $form->get('lastname')->getData();
				if($lastname !== $currentUser->getLastname()){
					$currentUser->setLastname($lastname);
					$updated = true;
				}
				
				$email = $form->get('email')->getData();
				if($email !== $currentUser->getEmail()){
					$currentUser->setEmail($email);
					$updated = true;
				}
				
				$twitter = $form->get('twitter')->getData();
				if($twitter !== $currentUser->getTwitter()){
					$currentUser->setTwitter($twitter);
					$updated = true;
				}
				
				$message = "Profile Updated Sucessfully!";
				
				// check if new password is valid and update user password 
				$newPassConstraint = new PasswordConstraint();
				if($passChange){
					$error = $this->get('validator')->validateValue(
					$form->get('plainPassword')->get('new')->getData(),
					$newPassConstraint);
					
					if(count($error)!=0){
						$form->get('plainPassword')->addError(new FormError($error[0]->getMessage()));
						$message = "Profile Updated Sucessfully although your Password was <strong>NOT Changed!</strong>. Please fix the errors and try again.";
					}
					else{
						$currentUser->setPlainPassword($form->get('plainPassword')->get('new')->getData());
						$this->um->updatePassword($currentUser);
						$updated = true;
						$message = "Profile and Password Updated Sucessfully!";
					}
				}
				
				if($updated){
					//update user's info in newsletter mailing list
					$api = new MCAPI($this->listapi);
					$merge_vars = array("FNAME"=>$firstname, "LNAME"=>$lastname, "EMAIL"=>$email);
					$api->listUpdateMember($this->listid, $currentUser->getEmail(), $merge_vars, false);
					
					$this->em->flush();
					$this->um->reloadUser($currentUser);
				}				
			}
			else
				$message = "<strong>Your Profile was NOT updated, please fix the errors before trying again.</strong>";
			
			//get errors from fields and store them in an assosiative array
			$content = array();
			$content = $this->getErrorMessages($form);
			$content['message'] = $message;
			
			return new Response(json_encode($content));
        }
        else
			return new Response($this->templating->render('AceUserBundle:Default:options.html.twig', array('form' => $form->createView(), 'image' => $image, "user" => $currentUser)));
		

    }
    
    private function isCurrentPass(Form $form, $currentPassword){
		
			return $this->comparePassword($currentPassword);
	}
    
    public function isCurrentPasswordAction(){
		
		if("POST" === $this->request->getMethod()){
			$currentPassword = $this->request->get('currentPassword');
			$return = $this->comparePassword($currentPassword);
			$response = array('valid' => $return);
							
			return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
		}
	}
	
	private function comparePassword($currentPassword){	
		
		$currentUser = $this->sc->getToken()->getUser();
		$encoder = $this->ef->getEncoder($currentUser);
		$encodedPass = $encoder->encodePassword($currentPassword, $currentUser->getSalt());
			
		if($encodedPass === $currentUser->getPassword())
			return true;
		
		return false;		
	}
    
    public function isEmailAvailableAction(){
		
		if("POST" === $this->request->getMethod()){
			$currentUser = $this->sc->getToken()->getUser();
			$email = $this->request->get('email');
			
			// TODO: find out why $this->get('usercontroller')->emailExistsAction($email) doesn't work
			$exists = $this->em->getRepository('AceUserBundle:User')->findOneByEmail($email);
			if($exists){
					if($email !== $currentUser->getEmail())
						$return = 'inUse'; //in use by another member
					else
						$return = 'own'; //already stored
			}
			else
				$return = 'available'; //success! New available email
				
			$response = array('valid' => $return);
							
			return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
		}
	}
    
    private function getErrorMessages(Form $form) {
    
		$errors = array();
		foreach ($form->getErrors() as $key => $error) {
			$template = $error->getMessageTemplate();
			$parameters = $error->getMessageParameters();

			foreach($parameters as $var => $value){
				$template = str_replace($var, $value, $template);
			}

			$errors[$key] = $template;
		}
		if ($form->hasChildren()) {
			foreach ($form->getChildren() as $child) {
				if (!$child->isValid()) {
					$errors[$child->getName()] = $this->getErrorMessages($child);
				}
			}
		}

		return $errors;
	}
 
	public function __construct(EngineInterface $templating,
								SecurityContext $securityContext,
								ContainerInterface $container,
								Request $request,
								UserManagerInterface $userManager,
								EncoderFactory $encoderFactory,
								EntityManager $entityManager,
								$listapi,
								$listid)
	{
		$this->templating = $templating;
		$this->sc = $securityContext;
		$this->container = $container;
		$this->request=$request;
		$this->um=$userManager;
		$this->ef=$encoderFactory;
		$this->em=$entityManager;
		$this->listapi=$listapi;
		$this->listid=$listid;
	}

}

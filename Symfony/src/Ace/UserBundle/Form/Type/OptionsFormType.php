<?php

namespace Ace\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\MaxLength;
use Symfony\Component\Validator\Constraints\MinLength;
use Symfony\Component\Validator\Constraints\Collection;


class OptionsFormType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
		
		/* create the form*/
        $builder
            ->add('username', 'text', array('read_only' => true ))
            ->add('firstname', 'text',array('attr' => array('onkeyup' => 'validation(id)')))
            ->add('lastname', 'text',array('attr' => array('onkeyup' => 'validation(id)')))
            ->add('email', 'email', array('attr' => array('onkeyup' => 'validation(id)')))
            ->add('twitter', 'text', array( 'required' => false ))
            ->add('currentPassword', 'password', array(
												'label' => 'Old Password',
												//'error_bubbling' => true,
            									'required' => false,
		        								'attr'=> array(
				    											'onkeyup' => 'oldpasscheck(id)',
				    											'onblur' => 'validation(id)',
				    											'placeholder'=> 'Type your current password'),
            									))
            ->add('plainPassword', 'repeated', array(
												'label' => 'New Password',
												'type' => 'password',
												'invalid_message' => 'The New Password fields must match.',
												'first_name' => 'new',
												'second_name' => 'confirm',
												//'error_bubbling' => true,
												'required' => false,
												'options' => array(
														 'attr' => array(
																	'onkeyup' => 'validation(id)',
																	'max_length' => 15,
																	'placeholder'=> 'Type your new password')),
												));
    }
    
    public function getDefaultOptions(array $options)
    {
		$constraints = new Collection(array(
			'fields' => array(
					'firstname' => array(
									new Regex( array(
													'pattern' => '/\d/',
													'match' => false,
													'message' => 'Your Firstname cannot contain a number'
													)),
									),
					'lastname' => array(
									new Regex( array(
													'pattern' => '/\d/',
													'match' => false,
													'message' => 'Your Lastname cannot contain a number'
													)),
									),
					'email' => array(
								new NotBlank(array('message' => 'Please fill in your email address')),
								new Email(array('message' => 'Invalid email address', 'checkMX' => true)),
								),
					'plainPassword' => array(
										new MinLength(array('limit' => 6, 'message' => 'Password must be at least 6 characters long')),
										new MaxLength(array('limit' => 15, 'message' => 'Password cannot be longer than 15 characters')),
								),
					),
					'allowExtraFields' => true,
					'allowMissingFields' => true,
				));
        
        return array(
            'validation_constraint' => $constraints,
        );
    }

    public function getName()
    {
        return 'options';
    }
    
}

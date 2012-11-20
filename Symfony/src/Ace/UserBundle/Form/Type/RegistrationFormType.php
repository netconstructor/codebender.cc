<?php

namespace Ace\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
		$builder
			->add('firstname', 'text', array('label' => 'user_registration_form_firstname',	'required' => false))
			->add('lastname', 'text', array('label' => 'user_registration_form_lastname', 'required' => false))
			->add('twitter', 'text', array('label' => 'user_registration_form_twitter',	'required' => false));
    }

    public function getName()
    {
        return 'ace_user_registration';
    }
}


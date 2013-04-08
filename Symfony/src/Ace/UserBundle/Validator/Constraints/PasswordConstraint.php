<?php

namespace Ace\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordConstraint extends Constraint
{
	public $message = 'Sorry, your New Password is too simple, try mix and matching Letters, Numbers or Symbols, to make it more secure.';
}

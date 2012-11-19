<?php

namespace Ace\UserBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordConstraint extends Constraint
{
	public $message = 'Password must contain at least 3 of 4 charsets: Lowercase, Uppercase, Numbers and Symbols';
}

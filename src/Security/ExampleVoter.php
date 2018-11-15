<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 23/10/2018
 * Time: 14:41
 */

namespace App\Security;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ExampleVoter implements VoterInterface
{
	public function vote(TokenInterface $token, $subject, array $attributes)
	{
		// TODO: Implement vote() method.
	}

}
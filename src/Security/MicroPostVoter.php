<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 31/10/2018
 * Time: 12:08
 */

namespace App\Security;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MicroPostVoter extends Voter
{

	const EDIT = 'edit';
	const DELETE = 'delete';

	/**
	 * @var AccessDecisionManagerInterface
	 */
	private $accessDecisionManager;

	public function __construct( AccessDecisionManagerInterface $accessDecisionManager )
	{

		$this->accessDecisionManager = $accessDecisionManager;
	}

	protected function supports($attribute, $subject)
	{
		if ( !in_array( $attribute, [ self::EDIT, self::DELETE ] ) ) {
			return false;
		}

		if ( !$subject instanceof MicroPost ){
			return false;
		}

		return true;
	}

	/**
	 * @param string $attribute
	 * @param mixed $subject
	 * @param TokenInterface $token
	 * @return bool|void
	 */
	protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
	{

		if ( $this->accessDecisionManager->decide( $token, [ User::ROLE_ADMIN ] ) ) {
			return true;
		}

		$authenticatedUser = $token->getUser();

		if ( !$authenticatedUser instanceof User ){
			return false;
		}

		/** @var MicroPost $microPost */
		$microPost = $subject;

		return ( $microPost->getUser()->getId() === $authenticatedUser->getId() );

	}


}
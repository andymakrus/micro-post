<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 02/11/2018
 * Time: 17:50
 */

namespace App\Controller;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FollowingController
 * @package App\Controller
 * @Security("is_granted('ROLE_USER')")
 * @Route("/following")
 */
class FollowingController extends Controller
{
	/**
	 * @Route("/follow/{id}", name="following_follow")
	 * @param User $userToFollow
	 */
	public function follow(User $userToFollow)
	{
		/** @var User $currentUser */
		$currentUser = $this->getUser();

		if ( $userToFollow->getId() != $currentUser->getId() ){

			$currentUser->follow($userToFollow);

			$this->getDoctrine()->getManager()->flush();

		}

		return $this->redirectToRoute(
			'micro_post_user',
			['username' => $userToFollow->getUsername()]
		);

	}

	/**
	 * @Route("/unfollow/{id}", name="following_unfollow")
	 * @param User $userToUnFollow
	 */
	public function unfollow(User $userToUnFollow)
	{
		/** @var User $currentUser */
		$currentUser = $this->getUser();

		$currentUser->getFollowing()->removeElement( $userToUnFollow );

		$this->getDoctrine()->getManager()->flush();

		return $this->redirectToRoute(
			'micro_post_user',
			['username' => $userToUnFollow->getUsername()]
		);
	}

}
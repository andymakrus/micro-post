<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 08/11/2018
 * Time: 17:15
 */

namespace App\Controller;


use App\Entity\MicroPost;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LikesController
 * @package App\Controller
 * @Route("/likes")
 */
class LikesController extends Controller
{
	/**
	 * @param MicroPost $microPost
	 * @Route("/like/{id}", name="likes_like")
	 */
	public function like(MicroPost $microPost)
	{
		/** @var User $currentUser */
		$currentUser = $this->getUser();

		if (!$currentUser instanceof User){
			return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
		}

		$microPost->like($currentUser);

		$this->getDoctrine()->getManager()->flush();

		return new JsonResponse([
				'count' => $microPost->getLikedBy()->count()
			]);

	}

	/**
	 * @param MicroPost $microPost
	 * @Route("/unlike/{id}", name="likes_unlike")
	 */
	public function unlike(MicroPost $microPost)
	{
		/** @var User $currentUser */
		$currentUser = $this->getUser();

		if (!$currentUser instanceof User){
			return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
		}

		$microPost->getLikedBy()->removeElement($currentUser);
		$this->getDoctrine()->getManager()->flush();

		return new JsonResponse([
			'count' => $microPost->getLikedBy()->count()
		]);

	}

}
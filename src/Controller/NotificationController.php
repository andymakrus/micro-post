<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 09/11/2018
 * Time: 11:51
 */

namespace App\Controller;


use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NotificationController
 * @package App\Controller
 * @Security("is_granted('ROLE_USER')")
 * @Route("/notification")
 */
class NotificationController extends Controller
{

	/**
	 * @var NotificationRepository
	 */
	private $notificationRepository;

	/**
	 * NotificationController constructor.
	 * @param NotificationRepository $notificationRepository
	 */
	public function __construct(NotificationRepository $notificationRepository)
	{
		$this->notificationRepository = $notificationRepository;
	}

	/**
	 * @Route("/unread-count", name="notification_unread")
	 */
	public function unreadCount()
	{
		try{

			$response = new JsonResponse([
				'count' => $this->notificationRepository->findUnseenByUser($this->getUser())
			]);

			return $response;

		} catch (NonUniqueResultException $nonUniqueResultException){
			return new JsonResponse([], Response::HTTP_NO_CONTENT);
		}
	}

	/**
	 * @Route("/all", name="notification_all")
	 */
	public function notifications()
	{
		try{
			return $this->render('notification/notifications.html.twig',[
				'notifications' => $this->notificationRepository->findBy([
					'seen' => false,
					'user' => $this->getUser()
				]),
			]);
		} catch (NonUniqueResultException $exception){
			return new Response([], Response::HTTP_UNPROCESSABLE_ENTITY);
		}
	}

	/**
	 * @param Notification $notification
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * @Route("/acknowledge/{id}", name="notification_acknowledge")
	 */
	public function acknowledge(Notification $notification)
	{
		$notification->setSeen(true);
		$this->getDoctrine()->getManager()->flush();
		return $this->redirectToRoute('notification_all');
	}

	/**
	 * @Route("/acknowledge-all", name="notification_acknowledge_all")
	 */
	public function acknowledgeAll()
	{
		$this->notificationRepository->markAllAsReadByUser($this->getUser());
		return $this->redirectToRoute('notification_all');
	}


}
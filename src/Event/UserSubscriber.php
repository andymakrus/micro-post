<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 12/11/2018
 * Time: 12:53
 */

namespace App\Event;


use App\Entity\UserPreferences;
use App\Mailer\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
	/**
	 * @var Mailer
	 */
	private $mailer;
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	/**
	 * @var string
	 */
	private $defaultLocale;

	/**
	 * Returns an array of event names this subscriber wants to listen to.
	 *
	 * The array keys are event names and the value can be:
	 *
	 *  * The method name to call (priority defaults to 0)
	 *  * An array composed of the method name to call and the priority
	 *  * An array of arrays composed of the method names to call and respective
	 *    priorities, or 0 if unset
	 *
	 * For instance:
	 *
	 *  * array('eventName' => 'methodName')
	 *  * array('eventName' => array('methodName', $priority))
	 *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
	 *
	 * @param Mailer $mailer
	 * @param EntityManagerInterface $entityManager
	 * @param string $defaultLocale
	 */

	public function __construct(Mailer $mailer, EntityManagerInterface $entityManager, string $defaultLocale)
	{

		$this->mailer = $mailer;
		$this->entityManager = $entityManager;
		$this->defaultLocale = $defaultLocale;
	}

	public static function getSubscribedEvents()
	{
		return [
			UserRegisterEvent::NAME => 'onUserRegister',
		];
	}

	public function onUserRegister(UserRegisterEvent $userRegisterEvent)
	{
		$registeredUser = $userRegisterEvent->getRegisteredUser();

		$preferences = new UserPreferences();
		$preferences->setLocale($this->defaultLocale);

		$registeredUser->setPreferences($preferences);
		$this->entityManager->flush();

		$this->mailer->sendConfirmationEmail( $registeredUser );
	}

}
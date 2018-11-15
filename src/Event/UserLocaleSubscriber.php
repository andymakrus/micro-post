<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 13/11/2018
 * Time: 20:16
 */

namespace App\Event;


use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLocaleSubscriber implements EventSubscriberInterface
{
	/**
	 * @var SessionInterface
	 */
	private $session;

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
	 * @param SessionInterface $session
	 */

	public function __construct(SessionInterface $session)
	{
		$this->session = $session;
	}

	public static function getSubscribedEvents()
	{
		return [
			SecurityEvents::INTERACTIVE_LOGIN => [
				[
					'onInteractiveLogin',
					15
				]
			]
		];
	}

	public function onInteractiveLogin(InteractiveLoginEvent $loginEvent)
	{
		/** @var User $user */
		$user = $loginEvent->getAuthenticationToken()->getUser();

		$this->session->set('_locale', $user->getPreferences()->getLocale());

	}

}
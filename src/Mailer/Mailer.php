<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 12/11/2018
 * Time: 17:10
 */

namespace App\Mailer;


use App\Entity\User;

class Mailer
{
	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	/**
	 * @var string
	 */
	private $mailFrom;

	public function __construct(
		\Swift_Mailer $mailer,
		\Twig_Environment $twig,
		string $mailFrom
		)
	{

		$this->mailer = $mailer;
		$this->twig = $twig;
		$this->mailFrom = $mailFrom;
	}

	public function sendConfirmationEmail(User $user)
	{


		$body = $this->twig->render('email/registration.html.twig', [
			'user' => $user,
		]);


		$message = ( new \Swift_Message() )
			->setSubject('Welcome to micro-post app')
			->addFrom($this->mailFrom)
			->setTo($user->getEmail(), $user->getFullName())
			->setBody($body, 'text/html');

		$this->mailer->send($message);
	}

}
<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 15/11/2018
 * Time: 13:12
 */

namespace App\Tests\Mailer;


use App\Entity\User;
use App\Mailer\Mailer;
use PHPUnit\Framework\TestCase;

class MailerTest extends TestCase
{
	public function testConfirmationEmail()
	{
		/** @var User $user */
		$user = new User();
		$user->setEmail('john@doe.com');

		$swiftMailerMock = $this->getMockBuilder( \Swift_Mailer::class )
			->disableOriginalConstructor()
			->getMock();
		$swiftMailerMock->expects($this->once())->method('send')
			->with($this->callback(function($subject){
				$messageStr = (string)$subject;
				return strpos($messageStr, "From: me@ssd.com") !== false
					&& strpos($messageStr, "Content-Type: text/html; charset=utf-8") !== false
					&& strpos($messageStr, "john@doe.com" ) !== false
					&& strpos($messageStr, "This is a message body" ) !== false;
			}));

		$twigMock = $this->getMockBuilder(\Twig_Environment::class)
			->disableOriginalConstructor()
			->getMock();
		$twigMock->expects($this->once())->method('render')
			->with(
				'email/registration.html.twig',
					[
						'user' => $user,
					]
				)
			->willReturn('This is a message body');

		$mailer = new Mailer( $swiftMailerMock, $twigMock, 'me@ssd.com' );
		$mailer->sendConfirmationEmail($user);

	}
}
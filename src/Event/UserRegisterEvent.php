<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 12/11/2018
 * Time: 12:29
 */

namespace App\Event;


use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
	const NAME = 'user.register';
	/**
	 * @var User
	 */
	private $registeredUser;

	public function __construct(User $registeredUser)
	{

		$this->registeredUser = $registeredUser;
	}

	public function getRegisteredUser()
	{
		return $this->registeredUser;
	}
}
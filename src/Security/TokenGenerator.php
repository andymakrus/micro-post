<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 12/11/2018
 * Time: 19:47
 */

namespace App\Security;


class TokenGenerator
{
	public function getRandomSecureToken(int $length): string
	{

		$token = '';

		try{
			$token = bin2hex(random_bytes($length / 2));
		} catch (\Exception $exception){

		}

		return $token;

	}
}
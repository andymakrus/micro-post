<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 14/11/2018
 * Time: 15:56
 */

namespace App\Tests\Security;


use App\Security\TokenGenerator;
use PHPUnit\Framework\TestCase;

class TokenGeneratorTest extends TestCase
{
	public function testTokenGeneration()
	{
		$tokenGen = new TokenGenerator();
		$resultToken = $tokenGen->getRandomSecureToken(30);
		$this->assertEquals(30, strlen($resultToken));
		$this->assertTrue(ctype_alnum($resultToken), 'Token contains incorrect characters');
	}
}
<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 23/10/2018
 * Time: 14:02
 */

namespace App\Service;

use Psr\Log\LoggerInterface;

class Greeting
{
	/*
	 * @var LoggerInterface
	 */
	private $logger;

	/*
	 * @var string
	 */
	private $message;




	public function __construct( LoggerInterface $logger, string $message )
	{
		$this->logger = $logger;
		$this->message = $message;
	}

	public function greet(string $name) : string
	{
		$this->logger->info( "Greeted $name" );
		return "{$this->message} $name";
	}
}
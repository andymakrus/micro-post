<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * Class Notification
 * @package App\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"like" = "LikeNotification"})
 */
abstract class Notification
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
	 * @var
	 * @ORM\ManyToOne(targetEntity="App\Entity\User")
	 */
	private $user;

	/**
	 * @var
	 * @ORM\Column(type="boolean")
	 */
	private $seen;

	public function __construct()
	{
		$this->seen = false;
	}

    public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param mixed $user
	 */
	public function setUser($user): void
	{
		$this->user = $user;
	}

	/**
	 * @return mixed
	 */
	public function getSeen()
	{
		return $this->seen;
	}

	/**
	 * @param mixed $seen
	 */
	public function setSeen($seen): void
	{
		$this->seen = $seen;
	}



}

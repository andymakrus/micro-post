<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="This e-mail is already used")
 * @UniqueEntity(fields="username", message="This username is already taken")
 */
class User implements AdvancedUserInterface, \Serializable
{

	const ROLE_USER = 'ROLE_USER';
	const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
	 * @var
	 * @ORM\Column(type="string", length=50, unique=true)
	 * @Assert\NotBlank()
	 * @Assert\Length(min=5, max=50)
	 */
    private $username;


	/**
	 * @var
	 * @Assert\NotBlank()
	 * @Assert\Length(min=8, max="4096")
	 */
	private $plainPassword;

	/**
	 * @var
	 * @ORM\Column(type="string")
	 */
    private $password;

	/**
	 * @var
	 * @ORM\Column(type="string", length=256, unique=true)
	 * @Assert\NotBlank()
	 * @Assert\Email()
	 */
    private $email;

	/**
	 * @var
	 * @ORM\Column(type="string")
	 */
    private $fullName;

	/**
	 * @var
	 * @ORM\Column(type="simple_array")
	 */
    private $roles;


	/**
	 * @var
	 * @ORM\OneToMany(targetEntity="App\Entity\MicroPost", mappedBy="user")
	 */
    private $posts;

	/**
	 * @var
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="following")
	 */
    private $followers;

	/**
	 * @var
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="followers")
	 * @ORM\JoinTable(name="following", joinColumns={
 	 *          @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 *     }, inverseJoinColumns={
	 *          @ORM\JoinColumn(name="following_user_id", referencedColumnName="id")
	 *     })
	 */
    private $following;

	/**
	 * @var
	 * @ORM\ManyToMany(targetEntity="App\Entity\MicroPost", mappedBy="likedBy")
	 *
	 */
    private $postsLiked;

	/**
	 * @var
	 * @ORM\Column(type="string", nullable=true, length=30)
	 */
    private $confirmationToken;

	/**
	 * @var
	 * @ORM\Column(type="boolean")
	 */
    private $enabled;

	/**
	 * @var
	 * @ORM\OneToOne(targetEntity="App\Entity\UserPreferences", cascade={"persist"})
	 */
    private $preferences;


    public function __construct()
    {
    	$this->posts = new ArrayCollection();
    	$this->followers = new ArrayCollection();
    	$this->following = new ArrayCollection();
    	$this->postsLiked = new ArrayCollection();
	    $this->roles = [self::ROLE_USER];
	    $this->enabled = false;
    }

	/**
	 * @return int|null
	 */
	public function getId(): ?int
    {
        return $this->id;
    }

	/**
	 * @return mixed
	 */
	public function getRoles()
	{
		return $this->roles;
	}

	/**
	 * @param array $roles
	 */
	public function setRoles(array $roles ): void
	{
		$this->roles = $roles;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function getSalt()
	{
		return null;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function eraseCredentials()
	{
		// TODO: Implement eraseCredentials() method.
	}

	/**
	 * @param mixed $username
	 */
	public function setUsername($username): void
	{
		$this->username = $username;
	}

	/**
	 * @param mixed $password
	 */
	public function setPassword($password): void
	{
		$this->password = $password;
	}
	/**
	 * @return mixed
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @param mixed $email
	 */
	public function setEmail($email): void
	{
		$this->email = $email;
	}

	/**
	 * @return mixed
	 */
	public function getFullName()
	{
		return $this->fullName;
	}

	/**
	 * @param mixed $fullName
	 */
	public function setFullName($fullName): void
	{
		$this->fullName = $fullName;
	}

	public function serialize()
	{
		return serialize([
			$this->id,
			$this->username,
			$this->password,
			$this->enabled
		]);
	}

	public function unserialize($serialized)
	{
		list(
			$this->id,
			$this->username,
			$this->password,
			$this->enabled
			) = unserialize($serialized);
	}

	/**
	 * @return mixed
	 */
	public function getPlainPassword()
	{
		return $this->plainPassword;
	}

	/**
	 * @param mixed $plainPassword
	 */
	public function setPlainPassword($plainPassword): void
	{
		$this->plainPassword = $plainPassword;
	}

	/**
	 * @return mixed
	 */
	public function getPosts()
	{
		return $this->posts;
	}

	/**
	 * @return Collection
	 */
	public function getFollowers()
	{
		return $this->followers;
	}

	/**
	 * @param mixed $followers
	 */
	public function setFollowers($followers): void
	{
		$this->followers = $followers;
	}

	/**
	 * @return Collection
	 */
	public function getFollowing()
	{
		return $this->following;
	}

	/**
	 * @param mixed $following
	 */
	public function setFollowing($following): void
	{
		$this->following = $following;
	}

	public function follow(User $userToFollow)
	{
		if ($this->getFollowing()->contains($userToFollow))
			return;

		$this->getFollowing()->add($userToFollow);

	}

	/**
	 * @return Collection
	 */
	public function getPostsLiked()
	{
		return $this->postsLiked;
	}

	/**
	 * @return mixed
	 */
	public function getConfirmationToken()
	{
		return $this->confirmationToken;
	}

	/**
	 * @param mixed $confirmationToken
	 */
	public function setConfirmationToken($confirmationToken): void
	{
		$this->confirmationToken = $confirmationToken;
	}

	/**
	 * @return mixed
	 */
	public function getEnabled()
	{
		return $this->enabled;
	}

	/**
	 * @param mixed $enabled
	 */
	public function setEnabled($enabled): void
	{
		$this->enabled = $enabled;
	}


	public function isAccountNonExpired()
	{
		return true;
	}

	public function isAccountNonLocked()
	{
		return true;
	}

	public function isCredentialsNonExpired()
	{
		return true;
	}

	public function isEnabled()
	{
		return $this->enabled;
	}

	/**
	 * @return UserPreferences|null
	 */
	public function getPreferences()
	{
		return $this->preferences;
	}

	/**
	 * @param mixed $preferences
	 */
	public function setPreferences($preferences): void
	{
		$this->preferences = $preferences;
	}

}

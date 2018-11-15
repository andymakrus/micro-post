<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 25/10/2018
 * Time: 12:36
 */

namespace App\DataFixtures;


use App\Entity\MicroPost;
use App\Entity\User;
use App\Entity\UserPreferences;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

	private const LANGUAGES = [
		'en',
		'fr'
	];

	private const USERS = [
		[
			'username' => 'john_doe',
			'email' => 'john_doe@doe.com',
			'password' => 'logotip',
			'fullName' => 'John Doe',
			'roles' => [User::ROLE_USER]
		],
		[
			'username' => 'rob_stark',
			'email' => 'rob_stark@stark.com',
			'password' => 'logotip',
			'fullName' => 'Rob Stark',
			'roles' => [User::ROLE_USER]
		],
		[
			'username' => 'd_targarian',
			'email' => 'd_targarian@targarian.com',
			'password' => 'logotip',
			'fullName' => 'Daynaries Targarian',
			'roles' => [User::ROLE_USER]
		],
		[
			'username' => 'jon_snow',
			'email' => 'jon@targarian.com',
			'password' => 'logotip',
			'fullName' => 'Jon Snow',
			'roles' => [User::ROLE_ADMIN]
		],
	];

	private const POST_TEXT = [
		'Hello, how are you?',
		'I wish I could fly',
		'I know the light must come from sun',
		'The cloud if burning gas',
		'But who am I to steer the eye',
		'Through ever loving grace',
		'My thoughts were always at a glimpse',
		'My mind was pure',
	];

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder )
	{
		$this->passwordEncoder = $passwordEncoder;
	}

	public function load(ObjectManager $manager)
	{

		$this->loadUsers( $manager );
		$this->loadMicroPosts( $manager );

	}

	private function loadMicroPosts( ObjectManager $manager )
	{
		for ( $i = 0; $i < 30; $i++ ){
			$microPost = new MicroPost();
			$microPost->setText(self::POST_TEXT[rand(0, ( count(self::POST_TEXT)) - 1 )]);
			$randomPostDate = new \DateTime();
			$randomPostDate->modify( '-'.rand(0, 10) . ' day' );
			$microPost->setTime( $randomPostDate );
			$microPost->setUser( $this->getReference( self::USERS[ rand(0, ( count( self::USERS ) - 1) ) ]['username'] ) );
			$manager->persist($microPost);
		}

		$manager->flush();
	}

	private function loadUsers( ObjectManager $manager )
	{

		foreach ( self::USERS as $user_data ){

			$user = new User();
			$user->setUsername( $user_data['username'] );
			$user->setFullName( $user_data['fullName'] );
			$user->setEmail( $user_data['email'] );
			$user->setRoles( $user_data['roles'] );
			$user->setPassword(
				$this->passwordEncoder->encodePassword(
					$user,
					$user_data['password']
				) );
			$user->setEnabled(true);
			$this->addReference($user_data['username'], $user);

			$preferences = new UserPreferences();
			$preferences->setLocale(self::LANGUAGES[rand(0,1)]);

			$user->setPreferences($preferences);

			$manager->persist($user);
		}

		$manager->flush();
	}

}
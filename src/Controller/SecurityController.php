<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 26/10/2018
 * Time: 13:32
 */

namespace App\Controller;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * SecurityController constructor.
	 * @param \Twig_Environment $twig
	 */
	public function __construct(\Twig_Environment $twig)
	{
		$this->twig = $twig;
	}

	/**
	 * @Route("/login", name="security_login")
	 */
	public function login( AuthenticationUtils $authenticationUtils )
	{
		return $this->render(
					'security/login.html.twig',
					[
						'last_username' => $authenticationUtils->getLastUsername(),
						'error' => $authenticationUtils->getLastAuthenticationError()

					]
				);
	}

	/**
	 * @Route("/logout", name="security_logout")
	 */
	public function logout()
	{

	}

	/**
	 * @Route("/confirm/{token}", name="security_confirm")
	 * @param string $token
	 * @param UserRepository $userRepository
	 * @param EntityManagerInterface $entityManager
	 * @return Response
	 * @throws \Twig_Error_Loader
	 * @throws \Twig_Error_Runtime
	 * @throws \Twig_Error_Syntax
	 */
	public function confirm(
		string $token,
		UserRepository $userRepository,
		EntityManagerInterface $entityManager)
	{
		$user = $userRepository->findOneBy([
			'confirmationToken' => $token
		]);

		if (null !== $user){
			$user->setEnabled(true);
			$user->setConfirmationToken('');
			try{
				$entityManager->flush();
			} catch ( \Exception $exception ) {

			}
		}

		return new Response($this->twig->render('security/confirmation.html.twig',[
			'user' => $user
		]));

	}

}
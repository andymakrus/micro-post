<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 25/10/2018
 * Time: 11:19
 */

namespace App\Controller;

use App\Entity\MicroPost;
use App\Entity\User;
use App\Repository\MicroPostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\MicroPostType;
use Symfony\Component\Routing\RouterInterface;
use \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class MicroPost
 * @package App\Controller
 * @Route("/micro-post")
 */
class MicroPostController extends AbstractController
{

	/**
	 * @var \Twig_Environment
	 */
	private $twig;

	/**
	 * @var MicroPostRepository
	 */
	private $microPostRepository;
	/**
	 * @var FormFactoryInterface
	 */
	private $formFactory;
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	/**
	 * @var RouterInterface
	 */
	private $router;
	/**
	 * @var FlashBagInterface
	 */
	private $flashBag;

	/**
	 * MicroPostController constructor.
	 * @param \Twig_Environment $twig
	 * @param MicroPostRepository $microPostRepository
	 * @param FormFactoryInterface $formFactory
	 * @param EntityManagerInterface $entityManager
	 * @param RouterInterface $router
	 * @param FlashBagInterface $flashBag
	 */
	public function __construct(
		\Twig_Environment $twig,
		MicroPostRepository $microPostRepository,
		FormFactoryInterface $formFactory,
		EntityManagerInterface $entityManager,
		RouterInterface $router,
		FlashBagInterface $flashBag
	)
	{

		$this->twig = $twig;
		$this->microPostRepository = $microPostRepository;
		$this->formFactory = $formFactory;
		$this->entityManager = $entityManager;
		$this->router = $router;
		$this->flashBag = $flashBag;
	}

	/**
	 * @Route("/", name="micro_post_index")
	 * @param TokenStorageInterface $tokenStorage
	 * @param UserRepository $userRepository
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function index( TokenStorageInterface $tokenStorage, UserRepository $userRepository )
	{
		/** @var User $currentUser */
		$currentUser = $tokenStorage->getToken()->getUser();

		$usersToFollow = [];

		if ( $currentUser instanceof User){
			$followedUsers = $currentUser->getFollowing();
			$posts = $this->microPostRepository->findAllByUsers( $followedUsers );

			$usersToFollow = count($posts) === 0 ?
				$userRepository->findAllWithMoreThanFivePostsExceptUser($currentUser)
				: [];

		} else {
			$posts = $this->microPostRepository->findBy(
				[],
				['time' => 'DESC']);
		}

		return $this->render('micro-post/index.html.twig',[
			'posts' => $posts,
			'usersToFollow' => $usersToFollow,
		]);

	}

	/**
	 * @Route("/add", name="micro_post_add")
	 * @Security("is_granted('ROLE_USER')")
	 */
	public function add( Request $request, TokenStorageInterface $tokenStorage )
	{

		$user = $tokenStorage->getToken()->getUser();

		$microPost = new \App\Entity\MicroPost();
		//$microPost->setTime( new \DateTime() ); // Lifecycled
		$microPost->setUser($user);

		$form = $this->formFactory->create( MicroPostType::class, $microPost );
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			$this->entityManager->persist($microPost);
			$this->entityManager->flush();
			return new RedirectResponse($this->router->generate('micro_post_index'));
		}

		return $this->render('micro-post/add.html.twig',[
			'form' => $form->createView()
		]);
	}

	/**
	 * @param MicroPost $microPost
	 * @param Request $request
	 * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 * @Route("/edit/{id}", name="micro_post_edit")
	 * @Security("is_granted('edit', microPost)")
	 */
	public function edit(MicroPost $microPost, Request $request)
	{

		$this->denyAccessUnlessGranted( 'edit', $microPost );

		$form = $this->formFactory->create( MicroPostType::class, $microPost );
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()){
			$this->entityManager->flush();
			return new RedirectResponse($this->router->generate('micro_post_index'));
		}

		return $this->render('micro-post/add.html.twig',[
			'form' => $form->createView()
		]);
	}

	/**
	 * @Route("/{id}", name="micro_post_post")
	 */
	public function post( MicroPost $post )
	{
		return $this->render('micro-post/post.html.twig',[
			'post' => $post,
		]);
	}

	/**
	 * @param MicroPost $microPost
	 * @return RedirectResponse
	 * @Route("/delete/{id}", name="micro_post_delete")
	 * @Security("is_granted('delete', microPost)")
	 */
	public function delete(MicroPost $microPost)
	{
		$this->entityManager->remove($microPost);
		$this->entityManager->flush();

		$this->flashBag->add('notice', 'Micro post was deleted');

		return new RedirectResponse($this->router->generate('micro_post_index'));
	}

	/**
	 * @param User $userWithPosts
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @Route("/user/{username}", name="micro_post_user")
	 */
	public function userPosts( User $userWithPosts )
	{
		return $this->render('micro-post/user-posts.html.twig',[
			'posts' => $this->microPostRepository->findBy(['user' => $userWithPosts ], ['time' => 'DESC']),
			'user' => $userWithPosts,
			//'posts' => $userWithPosts->getPosts(),
		]);
	}

}
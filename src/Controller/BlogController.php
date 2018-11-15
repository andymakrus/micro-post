<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 23/10/2018
 * Time: 14:08
 */

namespace App\Controller;


use App\Service\Greeting;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/blog")
 */
class BlogController extends Controller
{

	private $session;
	private $router;

	public function __construct(
		SessionInterface $session,
		RouterInterface $router
	)
	{
		$this->session = $session;
		$this->router = $router;
	}

	/**
	 * @Route("/", name="blog_index")
	 */
	public function index( )
	{
		return $this->render( 'blog/index.html.twig', [
			'posts' => $this->session->get('blog_posts')
		] );
	}

	/**
	 * @Route("/add", name="blog_add")
	 */
	public function add()
	{
		$posts = $this->session->get('blog_posts');
		$posts[uniqid()] = [
			'title' => 'A random title '.rand(1, 500),
			'text' => 'Some random text nr '.rand(1, 500),
			'date' => new \DateTime(),
		];
		$this->session->set('blog_posts', $posts);
		return new RedirectResponse( $this->router->generate('blog_index') );
	}

	/**
	 * @Route("/show/{id}", name="blog_show")
	 */
	public function show($id)
	{
		$posts = $this->session->get('blog_posts');

		if ( !$posts && !isset($posts[$id]) ) {
			throw new NotFoundHttpException('The requested post has not been found');
		}

		$html = $this->render(
			'blog/post.html.twig',
			[
				'id' => $id,
				'post' => $posts[$id],
			]
		);

		return $html;

	}


}
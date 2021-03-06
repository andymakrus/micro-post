<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllWithMoreThanFivePosts()
    {
    	$qb = $this->getFindAllWithMoreThenFivePostsQuery();
	    $query = $qb->getQuery();
    	return $query->getResult();
    }

    public function findAllWithMoreThanFivePostsExceptUser(User $user)
    {
	    $qb = $this->getFindAllWithMoreThenFivePostsQuery();
	    $qb->andHaving('u != :currentUser');
	    $qb->setParameter('currentUser', $user);
	    $query = $qb->getQuery();
	    return $query->getResult();
    }

    private function getFindAllWithMoreThenFivePostsQuery(): QueryBuilder
    {
	    $qb = $this->createQueryBuilder('u');

	    return $qb->select('u')
		    ->innerJoin('u.posts', 'mp')
		    ->groupBy('u')
		    ->having('count(mp) > 5');

    }

//    /**
//     * @return User[] Returns an array of User objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

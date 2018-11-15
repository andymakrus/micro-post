<?php
/**
 * Created by PhpStorm.
 * User: andy
 * Date: 09/11/2018
 * Time: 14:24
 */

namespace App\EventListener;


use App\Entity\LikeNotification;
use App\Entity\MicroPost;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LikeNotificationSubscriber implements EventSubscriberInterface
{

	public static function getSubscribedEvents()
	{
		return [
			Events::onFlush
		];
	}

	public function onFlush(OnFlushEventArgs $args)
	{
		$em = $args->getEntityManager();
		$uow = $em->getUnitOfWork();

		/** @var PersistentCollection $collectionUpdate */
		foreach($uow->getScheduledCollectionUpdates() as $collectionUpdate){

			if (!$collectionUpdate->getOwner() instanceof MicroPost){
				continue;
			}

			if ( 'likedBy' !== $collectionUpdate->getMapping()['fieldName'] ){
				continue;
			}

			$insertDiff = $collectionUpdate->getInsertDiff();

			if (!count($insertDiff)){
				return;
			}

			/** @var MicroPost $microPost */
			$microPost = $collectionUpdate->getOwner();

			$notification = new LikeNotification();
			$notification->setUser($microPost->getUser());
			$notification->setMicroPost($microPost);
			$notification->setLikedBy(reset($insertDiff));

			try{
				$em->persist($notification);
				$uow = $em->getUnitOfWork();
				$uow->computeChangeSet(
					$em->getClassMetadata(LikeNotification::class),
					$notification
				);

			} catch( ORMException $exception ) {

			}

		}
	}
}
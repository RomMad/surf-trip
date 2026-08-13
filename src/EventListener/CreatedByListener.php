<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Doctrine\Contract\CreatedByInterface as ContractCreatedByInterface;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs as PrePersistEventArgs;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist)]
final readonly class CreatedByListener
{
    public function __construct(
        private Security $security,
    ) {}

    /**
     * @param PrePersistEventArgs<ObjectManager> $event
     */
    public function prePersist(PrePersistEventArgs $event): void
    {
        $entity = $event->getObject();

        if (!$entity instanceof ContractCreatedByInterface) {
            return;
        }

        $user = $this->security->getUser();

        if ($user instanceof User) {
            $entity->setCreatedBy($user);
        }
    }
}

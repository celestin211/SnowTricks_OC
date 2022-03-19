<?php

namespace App\EventListener;

use App\Entity\Trick;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;


class TrickListener
{

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if ($entity instanceof Trick) {

            $entity->setUpdatedAt(new \DateTimeImmutable());
            $entity->setCreatedAt(new \DateTimeImmutable());
        }
    }
}

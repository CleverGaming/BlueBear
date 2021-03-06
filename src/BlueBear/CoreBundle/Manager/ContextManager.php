<?php

namespace BlueBear\CoreBundle\Manager;

use BlueBear\BaseBundle\Behavior\ManagerTrait;
use BlueBear\CoreBundle\Entity\Map\Context;
use BlueBear\CoreBundle\Entity\Map\ContextRepository;
use BlueBear\CoreBundle\Utils\Position;

class ContextManager
{
    use ManagerTrait;

    /**
     * Return a context with its map item from a starting position to a ending position
     *
     * @param $contextId
     * @param Position $startingPoint
     * @param Position $endingPoint
     * @return Context
     */
    public function findWithLimit($contextId, Position $startingPoint, Position $endingPoint)
    {
        return $this
            ->getRepository()
            ->findWithLimit(
                $contextId,
                (int)$startingPoint->x,
                (int)$startingPoint->y,
                (int)$endingPoint->x,
                (int)$endingPoint->y
            )
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Return current manager repository
     *
     * @return ContextRepository
     */
    protected function getRepository()
    {
        return $this->getEntityManager()->getRepository('BlueBearCoreBundle:Map\Context');
    }
}
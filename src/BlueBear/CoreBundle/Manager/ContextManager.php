<?php

namespace BlueBear\CoreBundle\Manager;

use BlueBear\CoreBundle\Manager\Behavior\ManagerBehavior;
use Doctrine\ORM\EntityRepository;

class ContextManager
{
    use ManagerBehavior;

    /**
     * Return current manager repository
     *
     * @return EntityRepository
     */
    protected function getRepository()
    {
        return $this->getEntityManager()->getRepository('BlueBearCoreBundle:Map\Context');
    }
}
<?php

namespace BlueBear\CoreBundle\Manager;

use BlueBear\BaseBundle\Behavior\ManagerTrait;
use BlueBear\CoreBundle\Entity\Map\Context;
use BlueBear\CoreBundle\Entity\Map\Layer;
use BlueBear\CoreBundle\Entity\Map\MapItem;
use BlueBear\CoreBundle\Entity\Map\MapItemRepository;
use BlueBear\CoreBundle\Entity\Map\Pencil;
use BlueBear\CoreBundle\Utils\Position;

class MapItemManager
{
    use ManagerTrait;

    /**
     * @param Position $position
     * @param Pencil $pencil
     * @param Layer $layer
     * @return MapItem
     */
    public function findByPositionPencilAndLayer(Position $position, Pencil $pencil, Layer $layer)
    {
        return $this->findOneBy([
            'x' => $position->x,
            'y' => $position->y,
            'layer' => $layer->getId(),
            'pencil' => $pencil->getId()
        ]);
    }

    /**
     * @param Context $context
     * @param Position $position
     * @param Layer $layer
     * @return MapItem
     */
    public function findByPositionAndLayer(Context $context, Position $position, Layer $layer)
    {
        return $this->findOneBy([
            'x' => $position->x,
            'y' => $position->y,
            'layer' => $layer->getId(),
            'context' => $context->getId()
        ]);
    }

    /**
     * Return mapItem repository
     *
     * @return MapItemRepository
     */
    protected function getRepository()
    {
        return $this
            ->getEntityManager()
            ->getRepository('BlueBear\CoreBundle\Entity\Map\MapItem');
    }
}

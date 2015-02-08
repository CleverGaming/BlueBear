<?php

namespace BlueBear\GameBundle\Event\MapItem;

use BlueBear\BaseBundle\Behavior\ContainerTrait;
use BlueBear\CoreBundle\Utils\Position;
use BlueBear\EngineBundle\Event\EngineEvent;
use BlueBear\EngineBundle\Event\MapItem\MapItemClickRequest;
use BlueBear\EngineBundle\Event\MapItem\MapItemClickResponse;
use BlueBear\GameBundle\Manager\UnitManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MapItemSubscriber implements EventSubscriberInterface
{
    use ContainerTrait;

    public static function getSubscribedEvents()
    {
        return [
            EngineEvent::ENGINE_ON_MAP_ITEM_CLICK => [
                'onClick'
            ]
        ];
    }

    public function onClick(EngineEvent $event)
    {
        /** @var MapItemClickRequest $request */
        $request = $event->getRequest();
        $position = new Position($request->x, $request->y);

        $unitInstance = $this->getUnitManager()->findInstanceByPosition($event->getContext(), $position);

        /** @BlueBearGameRule: if a map item with an unit is selected, the response must contains the list of available
         * cells for this unit according to its movement */
        if ($unitInstance) {
            die('lol');
        }

        $response = new MapItemClickResponse();
        $event->setResponse($response);
    }

    /**
     * @return UnitManager
     */
    protected function getUnitManager()
    {
        return $this->getContainer()->get('bluebear.manager.unit');
    }
}
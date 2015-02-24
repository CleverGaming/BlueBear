<?php

namespace BlueBear\BackofficeBundle\Controller;

use BlueBear\BaseBundle\Behavior\ControllerTrait;
use BlueBear\CoreBundle\Entity\Map\Context;
use BlueBear\CoreBundle\Entity\Map\Layer;
use BlueBear\CoreBundle\Entity\Map\Map;
use BlueBear\CoreBundle\Entity\Map\Pencil;
use BlueBear\CoreBundle\Entity\Map\PencilSet;
use BlueBear\CoreBundle\Manager\MapManager;
use BlueBear\EditorBundle\Event\Map\MapItemSubRequest;
use BlueBear\EditorBundle\Event\Map\MapUpdateRequest;
use BlueBear\EditorBundle\Event\Map\PutPencilRequest;
use BlueBear\EngineBundle\Event\EngineEvent;
use BlueBear\EngineBundle\Event\Map\LoadContextRequest;
use BlueBear\EngineBundle\Event\MapItem\MapItemClickRequest;
use BlueBear\GameBundle\Entity\EntityModel;
use BlueBear\GameBundle\Event\Entity\PutEntityRequest;
use JMS\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ApiController extends Controller
{
    use ControllerTrait;

    /**
     * Display api interface to send event and receive response from api
     *
     * @Template()
     */
    public function indexAction()
    {
        $form = $this->createForm('engine_event_test');
        $map = $this->getMapManager()->findOne();

        if (!$map) {
            $this->setMessage('You should create a map before calling api', 'error');
            return $this->redirect('@bluebear_backoffice_map');
        }
        $snippets = $this->getJsonSnippets($map);

        return [
            'form' => $form->createView(),
            'snippets' => $snippets
        ];
    }

    /**
     * Return json snippets to help user to make a request to api
     *
     * @param Map $map
     * @return string
     */
    protected function getJsonSnippets(Map $map)
    {
        $events = $this->get('bluebear.engine.engine')->getAllowedEvents();
        $snippets = [];
        /**
         * @var Context $context
         * @var Serializer $serializer
         */
        $context = $map->getContexts()->first();
        $serializer = $this->get('jms_serializer');

        foreach ($events as $event) {
            if ($event == EngineEvent::ENGINE_CONTEXT_LOAD) {
                $request = new LoadContextRequest();
                $request->contextId = $context->getId();
                $snippets[$event] = $request;
            } else if ($event == EngineEvent::ENGINE_MAP_ITEM_CLICK) {
                // get MapItemClick request
                $snippets[$event] = $this->getMapItemClickRequest($map, $context);
            } else if ($event == EngineEvent::ENGINE_MAP_PUT_ENTITY) {
                $snippets[$event] = $this->getPutEntityRequest($map, $context);
            } else if ($event == EngineEvent::EDITOR_MAP_PUT_PENCIL) {
                $snippets[$event] = $this->getPutPencilRequest($map, $context);
            } else if ($event == EngineEvent::EDITOR_MAP_UPDATE) {
                $snippets[$event] = $this->getMapUpdateRequest($map, $context);
            }
        }
        return $serializer->serialize($snippets, 'json');
    }

    protected function getMapItemClickRequest(Map $map, Context $context)
    {
        $pencilSet = $map->getPencilSets()->first();
        // event request
        $request = new MapItemClickRequest();
        $request->contextId = $context->getId();
        $request->x = 5;
        $request->y = 5;

        if ($pencilSet) {
            /** @var Pencil $pencil */
            $pencil = $pencilSet->getPencils()->first();
            $layers = [];

            /** @var Layer $layer */
            foreach ($map->getLayers() as $layer) {
                if ($pencil and $pencil->isLayerTypeAllowed($layer->getType())) {
                    $layers[] = $layer;
                }
            }
            if ($pencil and count($layers)) {
                $request->pencil = $pencil->getId();
                $layer = $layers[array_rand($layers)];
                $request->layer = $layer->getId();
            } else {
                $this->addFlash('warning', 'No layer was found. Try to create at least one');
            }
        } else {
            $this->addFlash('warning', 'No pencil set was found. Try to create at least one');
        }
        return $request;
    }

    protected function getPutPencilRequest(Map $map, Context $context)
    {
        $request = new PutPencilRequest();
        $request->contextId = $context->getId();
        $request->layerName = $this->getRandomLayer($map->getLayers()->toArray())->getName();
        $request->pencilName = $this->getRandomPencil($map->getPencilSets()->toArray())->getName();
        $request->x = 3;
        $request->y = 3;

        return $request;
    }

    protected function getPutEntityRequest(Map $map, Context $context)
    {
        $request = null;
        $entities = $this->get('bluebear.manager.entity_model')->findAll();
        $layers = [];

        if ($entities) {
            /** @var EntityModel $entityModel */
            $entityModel = $entities[array_rand($entities)];
            /** @var Layer $layer */
            foreach ($map->getLayers() as $layer) {
                if ($entityModel and $entityModel->isLayerTypeAllowed($layer->getType())) {
                    $layers[] = $layer;
                }
            }
            // TODO sort to have only unit model instance and make an other event with items
            $request = new PutEntityRequest();
            $request->contextId = $context->getId();
            $request->entityModelId = $entityModel->getId();
            $request->layerName = $this->getRandomLayer($layers)->getName();
            $request->x = 4;
            $request->y = 2;
        } else {
            $this->addFlash('warning', 'You have no entity model configured. PutEntity event is not available');
        }
        return $request;
    }

    protected function getMapUpdateRequest(Map $map, Context $context)
    {
        $request = new MapUpdateRequest();
        $request->contextId = $context->getId();
        $request->mapItems = [];
        $i = 0;

        while ($i < 3) {
            $subRequest = new MapItemSubRequest();
            $subRequest->x = rand(0, 10);
            $subRequest->y = rand(0, 10);
            $subRequest->layerName = $this->getRandomLayer($map->getLayers()->toArray())->getName();
            $subRequest->pencilName = $this->getRandomPencil($map->getPencilSets()->toArray())->getName();
            $request->mapItems[] = $subRequest;
            $i++;
        }
        return $request;
    }

    /**
     * @param Layer[] $layers
     * @return Layer
     */
    protected function getRandomLayer(array $layers)
    {
        return $layers[array_rand($layers)];
    }

    /**
     * @param PencilSet[] $pencilSets
     * @return Pencil
     */
    protected function getRandomPencil(array $pencilSets)
    {
        $pencils = [];

        foreach ($pencilSets as $pencilSet) {
            $pencils = array_merge($pencilSet->getPencils()->toArray(), $pencils);
        }
        return $pencils[array_rand($pencils)];
    }

    /**
     * @return MapManager
     */
    public function getMapManager()
    {
        return $this->get('bluebear.manager.map');
    }
} 
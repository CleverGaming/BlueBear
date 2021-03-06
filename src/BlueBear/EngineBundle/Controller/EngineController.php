<?php

namespace BlueBear\EngineBundle\Controller;

use BlueBear\BaseBundle\Behavior\ControllerTrait;
use BlueBear\EngineBundle\Engine\Engine;
use BlueBear\EngineBundle\Event\EngineEvent;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EngineController extends Controller
{
    use ControllerTrait;

    public function triggerEventAction(Request $request)
    {
        // api event name
        $eventName = $request->get('eventName');
        // api event data
        $eventData = $request->get('eventData');
        if (!$eventData) {
            $eventData = $request->getContent();
        }
        /** @var Engine $engine */
        $engine = $this->get('bluebear.engine.engine');
        $engineEvent = $engine->run($eventName, $eventData);
        /** @var Serializer $serializer */
        $serializer = $this->get('jms_serializer');
        $content = $serializer->serialize($engineEvent->getResponse(), 'json');

        $client = $this->get('elephantio_client.default');
        // Register async callback

//        $this->get('bluebear.kernel.terminate.listener')->addCallBack(function() use ($client, $content) {
            $client->send(EngineEvent::ENGINE_CLIENT_UPDATE, ['event' => $content]);
//        });

        $response = new Response();
        $response->setStatusCode(200);
        $response->setContent($content);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
} 

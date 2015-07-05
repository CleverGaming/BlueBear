<?php

namespace BlueBear\EngineBundle\Event\Response;

use BlueBear\EngineBundle\Event\EventResponse;

class AttackResponse extends EventResponse
{
    public $endPoint;

    public function setData($contextId, $gameId)
    {
        $this->data['contextId'] = $contextId;
        $this->data['gameId'] = $gameId;
    }
}

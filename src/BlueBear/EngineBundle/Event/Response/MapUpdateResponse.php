<?php

namespace BlueBear\EngineBundle\Event\Response;

use BlueBear\EngineBundle\Event\EventResponse;
use JMS\Serializer\Annotation as Serializer;

class MapUpdateResponse extends EventResponse
{
    public function setData($updated, $removed)
    {
        $this->data = [
            'updated' => $updated,
            'removed' => $removed,
        ];
    }
}

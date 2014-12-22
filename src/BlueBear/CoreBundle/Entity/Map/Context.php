<?php

namespace BlueBear\CoreBundle\Entity\Map;

use BlueBear\CoreBundle\Entity\Behavior\Data;
use BlueBear\CoreBundle\Entity\Behavior\Id;
use BlueBear\CoreBundle\Entity\Behavior\Label;
use BlueBear\CoreBundle\Entity\Behavior\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Context of a map
 *
 * @ORM\Table(name="context")
 * @ORM\Entity(repositoryClass="BlueBear\CoreBundle\Entity\Map\ContextRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Context
{
    use Id, Label, Timestampable, Data;

    /**
     * Map which this context belongs
     *
     * @ORM\ManyToOne(targetEntity="BlueBear\CoreBundle\Entity\Map\Map", inversedBy="contexts")
     */
    protected $map;

    /**
     * Map with this context as current context
     *
     * @ORM\OneToOne(targetEntity="BlueBear\CoreBundle\Entity\Map\Map", inversedBy="currentContext")
     */
    protected $currentMap;

    /**
     * Tiles for this context
     *
     * @ORM\OneToMany(targetEntity="BlueBear\CoreBundle\Entity\Map\Tile", mappedBy="context")
     */
    protected $tiles;

    /**
     * Convert the context to an array
     *
     * @return array
     */
    public function toArray()
    {
        $tiles = $this->getTiles();
        $tilesArray = [];

        /** @var Tile $tile */
        foreach ($tiles as $tile) {
            $tilesArray[] = $tile->toArray();
        }
        return [
            'id' => $this->getId(),
            'tiles' => $tilesArray
        ];
    }

    /**
     * @return Map
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @param mixed $map
     */
    public function setMap(Map $map)
    {
        $this->map = $map;
    }

    /**
     * @return mixed
     */
    public function getCurrentMap()
    {
        return $this->currentMap;
    }

    /**
     * @param mixed $currentMap
     */
    public function setCurrentMap($currentMap)
    {
        $this->currentMap = $currentMap;
    }

    /**
     * @return mixed
     */
    public function getTiles()
    {
        return $this->tiles;
    }

    /**
     * Return tiles with id as array key
     *
     * @return array
     */
    public function getTilesById()
    {
        $tiles = [];
        /** @var Tile $tile */
        foreach ($this->tiles as $tile) {
            $tiles[$tile->getId()] = $tile;
        }
        return $tiles;
    }

    /**
     * @param mixed $tiles
     */
    public function setTiles($tiles)
    {
        $this->tiles = $tiles;
    }
} 
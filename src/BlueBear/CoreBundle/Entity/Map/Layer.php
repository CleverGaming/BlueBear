<?php

namespace BlueBear\CoreBundle\Entity\Map;

use BlueBear\CoreBundle\Entity\Behavior\Descriptionable;
use BlueBear\CoreBundle\Entity\Behavior\Id;
use BlueBear\CoreBundle\Entity\Behavior\Label;
use BlueBear\CoreBundle\Entity\Behavior\Nameable;
use BlueBear\CoreBundle\Entity\Behavior\Typeable;
use Doctrine\ORM\Mapping as ORM;

/**
 * A layer of the map, containing a collection of positioned mapItems
 *
 * @ORM\Table(name="layer")
 * @ORM\Entity(repositoryClass="BlueBear\CoreBundle\Entity\Map\LayerRepository")
 */
class Layer
{
    use Id, Nameable, Label, Typeable, Descriptionable;

    /**
     * @ORM\OneToMany(targetEntity="BlueBear\CoreBundle\Entity\Map\PencilTile", mappedBy="layer")
     */
    protected $tiles;

    public function toArray()
    {
        $json = [
            'id' => $this->getId(),
            'label' => $this->getLabel()
        ];
        return $json;
    }

    /**
     * @return mixed
     */
    public function getTiles()
    {
        return $this->tiles;
    }

    /**
     * @param mixed $tiles
     */
    public function setTiles($tiles)
    {
        $this->tiles = $tiles;
    }
}
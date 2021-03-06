<?php

namespace BlueBear\CoreBundle\Entity\Map;

use BlueBear\CoreBundle\Entity\Behavior\Id;
use BlueBear\CoreBundle\Entity\Behavior\Positionable;
use BlueBear\GameBundle\Entity\EntityInstance;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * MapItem
 * A Map Item is an object that is positioned on the map at a specific layer and that will use the pencil as its renderer
 *
 * @ORM\Entity(repositoryClass="BlueBear\CoreBundle\Entity\Map\MapItemRepository")
 * @ORM\Table(name="map_item")
 * @Serializer\ExclusionPolicy("all")
 */
class MapItem
{
    use Id, Positionable;

    /**
     * @var Pencil
     * @ORM\ManyToOne(targetEntity="BlueBear\CoreBundle\Entity\Map\Pencil", inversedBy="mapItems")
     */
    protected $pencil;

    /**
     * Layer
     *
     * @var Layer
     * @ORM\ManyToOne(targetEntity="BlueBear\CoreBundle\Entity\Map\Layer", inversedBy="mapItems", fetch="EAGER");
     * @ORM\JoinColumn(nullable=false)
     */
    protected $layer;

    /**
     * @ORM\ManyToOne(targetEntity="BlueBear\CoreBundle\Entity\Map\Context", inversedBy="mapItems")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $context;

    /**
     * @Serializer\Expose()
     * @Serializer\AccessType("public_method")
     */
    protected $listeners = [];

    /**
     * @Serializer\Expose()
     * @Serializer\AccessType("public_method")
     */
    protected $path;

    public function getPencil()
    {
        return $this->pencil;
    }

    public function setPencil(Pencil $pencil)
    {
        $this->pencil = $pencil;
        return $this;
    }

    public function getLayer()
    {
        return $this->layer;
    }

    public function setLayer(Layer $layer)
    {
        $this->layer = $layer;
        return $this;
    }

    /**
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param Context $context
     */
    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("pencilName")
     * @return string
     */
    public function getPencilName()
    {
        if ($this->getPencil()) {
            return $this->getPencil()->getName();
        }
        return '';
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("layerName")
     * @return string
     */
    public function getLayerName()
    {
        if ($this->getLayer()) {
            return $this->getLayer()->getName();
        }
        return '';
    }

    public function getListeners()
    {
        return $this->listeners;
    }

    /**
     * @param mixed $listeners
     */
    public function setListeners($listeners)
    {
        $this->listeners = $listeners;
    }

    public function addListener($key, $listener)
    {
        $this->listeners[$key] = $listener;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }
}

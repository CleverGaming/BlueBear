<?php


namespace BlueBear\CoreBundle\Entity\Map;

use BlueBear\CoreBundle\Entity\Behavior\Id;
use BlueBear\CoreBundle\Entity\Behavior\Nameable;
use Doctrine\ORM\Mapping as ORM;

/**
 * A pencil set is a collection of pencil. Each have a pencil set.
 *
 * @ORM\Table(name="pencil_set")
 * @ORM\Entity(repositoryClass="BlueBear\CoreBundle\Entity\PencilSetRepository")
 */
class PencilSet
{
    use Id, Nameable;

    /**
     * List of pencils attached to the pencil set
     *
     * @ORM\OneToMany(targetEntity="BlueBear\CoreBundle\Entity\Map\Pencil", mappedBy="pencilSet")
     */
    protected $pencils;
} 
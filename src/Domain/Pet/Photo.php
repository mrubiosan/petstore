<?php
namespace Mrubiosan\PetStore\Domain\Pet;

/**
 * @Entity
 */
class Photo
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @Column(type="string")
     */
    private $url;

    /**
     * @ManyToOne(targetEntity="Pet", inversedBy="photos")
     * @JoinColumn(nullable=false)
     * @var Pet
     */
    private $pet;
}

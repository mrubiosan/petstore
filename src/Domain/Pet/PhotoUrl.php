<?php
namespace Mrubiosan\PetStore\Domain\Pet;

/**
 * @Entity
 */
class PhotoUrl implements \JsonSerializable
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
    private $url = '';

    /**
     * @ManyToOne(targetEntity="Pet", inversedBy="photos")
     * @JoinColumn(nullable=false)
     * @var Pet
     */
    private $pet;

    public function __construct(string $url, Pet $pet)
    {
        $this->setUrl($url);
        $this->pet = $pet;
    }

    private function setUrl(string $url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \InvalidArgumentException('Invalid URL');
        }

        $this->url = $url;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getUrl();
    }
}

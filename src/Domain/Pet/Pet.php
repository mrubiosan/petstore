<?php
namespace Mrubiosan\PetStore\Domain\Pet;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 */
class Pet implements \JsonSerializable
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    private $id;

    /**
     * @ManyToOne(targetEntity="Category", cascade={"all"})
     * @var Category|null
     */
    private $category;

    /**
     * @Column(type="string")
     * @var string
     */
    private $name = '';

    /**
     * @OneToMany(targetEntity="PhotoUrl", mappedBy="pet", cascade={"all"})
     */
    private $photoUrls;

    /**
     * @ManyToMany(targetEntity="Tag", cascade={"all"})
     */
    private $tags;

    /**
     * @Column(type="string", options={"default":"available"})
     * @var string
     */
    private $status = '';

    /**
     * @var string[]
     */
    const VALID_STATUSES = ['available', 'pending', 'sold'];

    public function __construct(int $id, string $name, string $status = 'available')
    {
        $this->id = $id;
        $this->name = $name;
        $this->photoUrls = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->setStatus($status);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category|null $category
     * @return Pet
     */
    public function setCategory(?Category $category): Pet
    {
        $this->category = $category;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PhotoUrl[]
     */
    public function getPhotoUrls(): array
    {
        return $this->photoUrls->toArray();
    }

    public function setPhotoUrls(PhotoUrl ...$photoUrls): Pet
    {
        $this->photoUrls->clear();
        foreach ($photoUrls as $photoUrl) {
            $this->photoUrls->add($photoUrl);
        }

        return $this;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags->toArray();
    }

    public function setTags(Tag ...$tags): Pet
    {
        $this->tags->clear();
        foreach ($tags as $tag) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): Pet
    {
        if (!in_array($status, self::VALID_STATUSES)) {
            throw new \InvalidArgumentException('Invalid status');
        }
        $this->status = $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return array_filter([
            'id' => $this->id,
            'category' => $this->getCategory(),
            'name' => $this->getName(),
            'photoUrls' => $this->getPhotoUrls(),
            'tags' => $this->getTags(),
            'status' => $this->getStatus(),
        ], function($val) { return $val !== null; });
    }
}

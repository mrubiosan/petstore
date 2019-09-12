<?php
namespace Mrubiosan\PetStore\Domain\Pet;

/**
 * @Entity
 */
class Pet
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
     * @OneToMany(targetEntity="Photo", mappedBy="pet")
     * @var Photo[]
     */
    private $photos = [];

    /**
     * @ManyToMany(targetEntity="Tag", cascade={"all"})
     * @var Tag[]
     */
    private $tags = [];

    /**
     * @Column(type="string", options={"default":"available"})
     * @var string
     */
    private $status = 'available';

    /**
     * @var string[]
     */
    private static $validStatuses = ['available', 'pending', 'sold'];

    /**
     * Pet constructor.
     * @param int      $id
     * @param string   $name
     * @param string[] $photoUrls
     */
    public function __construct(int $id, string $name, array $photoUrls)
    {
        $this->id = $id;
        $this->name = $name;
        $this->photoUrls = $photoUrls;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Pet
     */
    public function setId(int $id): Pet
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Category|null
     */
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

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Pet
     */
    public function setName(string $name): Pet
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getPhotoUrls(): array
    {
        return $this->photoUrls;
    }

    /**
     * @param string[] $photoUrls
     * @return Pet
     */
    public function setPhotoUrls(array $photoUrls): Pet
    {
        $invalidUrls = array_filter($photoUrls, function (string $url) {
            return filter_var($url, FILTER_VALIDATE_URL) === false;
        });

        if ($invalidUrls) {
            throw new \InvalidArgumentException("Invalid photo urls: ".implode(', ', $invalidUrls));
        }

        $this->photoUrls = $photoUrls;
        return $this;
    }

    /**
     * @return Tag[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param Tag ...$tags
     * @return Pet
     */
    public function setTags(Tag ...$tags): Pet
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     * @return Pet
     */
    public function setStatus(?string $status): Pet
    {
        if ($status !== null && !in_array($status, self::$validStatuses)) {
            throw new \InvalidArgumentException('Invalid status');
        }
        $this->status = $status;
        return $this;
    }
}

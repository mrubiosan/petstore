<?php
namespace Mrubiosan\PetStore\Presentation;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\ORMException;
use Fig\Http\Message\StatusCodeInterface;
use Mrubiosan\PetStore\Domain\Pet\Category;
use Mrubiosan\PetStore\Domain\Pet\Pet;
use Mrubiosan\PetStore\Domain\Pet\PhotoUrl;
use Mrubiosan\PetStore\Domain\Pet\Tag;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Webmozart\Assert\Assert;

class PetCreateController
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * PetCreateController constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function create(Request $request, Response $response)
    {
        $body = $request->getParsedBody();

        // Slim body parser middleware unserializes JSON into arrays, so we check for array but JSON input is an object
        try {
            Assert::nullOrIsArray($body['category'] ?? null, '"category" should be an object');
            Assert::string($body['name'] ?? null, '"name" should be a string');
            Assert::allString($body['photoUrls'] ?? null, '"photoUrls" should be an array of strings');
            Assert::nullOrIsArray($body['tags'] ?? null, '"tags" should be an array of objects');
            Assert::nullOrOneOf($body['status'] ?? null, Pet::VALID_STATUSES, '"status" should be one of: %2$s');

            $pet = new Pet(0, $body['name']);
            if (!empty($body['status'])) {
                $pet->setStatus($body['status']);
            }

            try {
                $photoUrls = array_map(function ($url) use ($pet) {
                    return new PhotoUrl($url, $pet);
                }, $body['photoUrls'] ?? []);
                $pet->setPhotoUrls(...$photoUrls);
            } catch (\InvalidArgumentException $e) {
                $response->getBody()->write('"photoUrls" contains invalid URLs');
                return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
            }

            if (!empty($body['category'])) {
                Assert::integerish($body['category']['id'] ?? null, '"category.id" should be an integer');
                Assert::string($body['category']['name'] ?? null, '"category.name" should be a string');
                $pet->setCategory($this->buildCategory($body['category']));
            }

            if (!empty($body['tags'])) {
                foreach ($body['tags'] as $i => $bodyTag) {
                    Assert::integerish($bodyTag['id'] ?? null, "\"tags[$i].id\" should be an integer");
                    Assert::string($bodyTag['name'] ?? null, "\"tags[$i].name\" should be a string");
                }
                $pet->setTags(...$this->buildTags($body['tags']));
            }
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        try {
            $this->em->persist($pet);
            $this->em->flush();
        } catch (ORMException $e) {
            $response = $response->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        $response->getBody()->write(json_encode($pet));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(StatusCodeInterface::STATUS_CREATED);
    }

    private function buildCategory(array $rawCategory)
    {
        /** @var EntityRepository $repo */
        $repo = $this->em->getRepository(Category::class);
        /** @var Category|null $category */
        if ($category = $repo->find($rawCategory['id'])) {
            $category->setName($rawCategory['name']);
        } else {
            $category = new Category($rawCategory['id'], $rawCategory['name']);
        }

        return $category;
    }

    private function buildTags(array $rawTags) : array
    {
        /** @var EntityRepository $repo */
        $repo = $this->em->getRepository(Tag::class);
        $bodyTagsMap = array_column($rawTags, null, 'id');
        $existingTags = $repo->findBy(['id' => array_keys($bodyTagsMap)]);
        $existingTagIds = array_map(function(Tag $tag) {
            return $tag->getId();
        }, $existingTags);
        $existingTagsMap = array_combine($existingTagIds, $existingTags);

        $tags = [];
        foreach ($bodyTagsMap as $tagId => $bodyTag) {
            if (array_key_exists($tagId, $existingTagsMap)) {
                $tags[] = $existingTagsMap[$tagId]->setName($bodyTag['name']);
            } else {
                $tags[] = new Tag($bodyTag['id'], $bodyTag['name']);
            }
        }

        return $tags;
    }
}

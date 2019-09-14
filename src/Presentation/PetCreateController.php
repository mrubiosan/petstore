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
        $pet = new Pet(0, $body['name']);
        if (!empty($body['status'])) {
            $pet->setStatus($body['status']);
        }

        $photoUrls = array_map(function($url) use ($pet) {
            return new PhotoUrl($url, $pet);
        }, $body['photoUrls'] ?? []);
        $pet->setPhotoUrls(...$photoUrls);

        if (!empty($body['category'])) {
            /** @var EntityRepository $repo */
            $repo = $this->em->getRepository(Category::class);
            if ($repo->find($body['category']['id']) !== null) {
                throw new \Exception("oops");
            }

            $pet->setCategory(new Category($body['category']['id'], $body['category']['name']));
        }

        if (!empty($body['tags'])) {
            /** @var EntityRepository $repo */
            $repo = $this->em->getRepository(Tag::class);
            $existingTags = $repo->findBy(['id' => array_column($body['tags'], 'id')]);
            if ($existingTags) {
                throw new \Exception('oops');
            }

            $tags = array_map(function($rawTag) {
                return new Tag($rawTag['id'], $rawTag['name']);
            }, $body['tags']);

            $pet->setTags(...$tags);
        }

        try {
            $this->em->persist($pet);
            $this->em->flush();
        } catch (ORMException $e) {
            $response = $response->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }
}

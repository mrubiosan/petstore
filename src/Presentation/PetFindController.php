<?php
namespace Mrubiosan\PetStore\Presentation;

use Doctrine\ORM\EntityManager;
use Fig\Http\Message\StatusCodeInterface;
use Mrubiosan\PetStore\Domain\Pet\Pet;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Webmozart\Assert\Assert;

class PetFindController
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function find(Request $request, Response $response)
    {
        $repo = $this->em->getRepository(Pet::class);
        $query = $request->getQueryParams();

        if (isset($query['status'])) {
            try {
                Assert::string($query['status'], '"status" should be a string');
                $statuses = explode(',', $query['status']);
                Assert::allOneOf($statuses, Pet::VALID_STATUSES, '"status" can take values %2$s');
            } catch (\InvalidArgumentException $e) {
                $response->getBody()->write($e->getMessage());
                return $response->withStatus(StatusCodeInterface::STATUS_BAD_REQUEST);
            }
            $pets = $repo->findBy(['status' => $statuses]);
        } else {
            $pets = $repo->findAll();
        }

        $response->getBody()->write(json_encode($pets));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

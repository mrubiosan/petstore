<?php
namespace Mrubiosan\PetStore\Presentation;

use Doctrine\ORM\EntityManager;
use Mrubiosan\PetStore\Domain\Pet\Pet;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

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
        $body = $request->getParsedBody();
        $statuses = explode(',', $body['status']);
        $repo = $this->em->getRepository(Pet::class);
        $pets = $repo->findBy(['status' => $statuses]);

        $response->getBody()->write(json_encode($pets));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

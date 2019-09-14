<?php
namespace Mrubiosan\PetStore\Presentation;

use Doctrine\ORM\EntityManager;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PetDeleteController
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function delete(Request $request, Response $response)
    {
        $pet = $request->getAttribute('pet');

        $this->em->remove($pet);
        $this->em->flush();

        return $response;
    }
}

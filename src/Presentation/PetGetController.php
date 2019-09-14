<?php
namespace Mrubiosan\PetStore\Presentation;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PetGetController
{
    public function get(Request $request, Response $response)
    {
        $pet = $request->getAttribute('pet');
        $response->getBody()->write(json_encode($pet));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

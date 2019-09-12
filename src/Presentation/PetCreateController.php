<?php
namespace Mrubiosan\PetStore\Presentation;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PetCreateController
{
    public function create(Request $request, Response $response)
    {
        $response->getBody()->write('foo');

        return $response;
    }
}

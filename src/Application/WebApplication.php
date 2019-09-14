<?php
namespace Mrubiosan\PetStore\Application;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Fig\Http\Message\StatusCodeInterface;
use Mrubiosan\PetStore\Domain\Pet\Pet;
use Mrubiosan\PetStore\Presentation\PetCreateController;
use Mrubiosan\PetStore\Presentation\PetDeleteController;
use Mrubiosan\PetStore\Presentation\PetGetController;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Interfaces\RouteInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

class WebApplication
{
    public function create() : App
    {
        $app = AppFactory::createFromContainer($this->createContainer());
        $app->addErrorMiddleware(true, true, true);
        $app->addBodyParsingMiddleware();
        $this->addRoutes($app);

        return $app;
    }

    private function addRoutes(App $app) : void
    {
        $app->group('/pet', function (RouteCollectorProxy $group) {
            $group->post('', PetCreateController::class.':create');
            $group->get('/{petId}', PetGetController::class.':get');
            $group->delete('/{petId}', PetDeleteController::class.':delete');
        })->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler) use ($app) {
            // Binds a pet instance when route contains petId
            /** @var RouteInterface $route */
            $route = $request->getAttribute('route');
            $petId = $route ? $route->getArgument('petId') : null;
            if ($petId !== null) {
                /** @var EntityManager $em */
                $em = $app->getContainer()->get('entityManager');
                $pet = $em->getRepository(Pet::class)->find((int) $petId);
                if (!$pet) {
                    return new Response(StatusCodeInterface::STATUS_NOT_FOUND);
                }
                $request = $request->withAttribute('pet', $pet);
            }
            return $handler->handle($request);
        });
    }

    private function createContainer() : PsrContainer
    {
        $container = new Container();

        $container['entityManager'] = function () {
            $dbParams = [
                'driver'   => 'pdo_mysql',
                'user'     => 'root',
                'password' => 'root',
                'dbname'   => 'dev',
                'host'     => '127.0.0.1',
                'port'     => 3316,
                'charset'  => 'utf8',
            ];

            $config = Setup::createAnnotationMetadataConfiguration([__DIR__.'/../Domain/Pet'], true);
            return EntityManager::create($dbParams, $config);
        };

        $container[PetCreateController::class] = function (Container $c) {
            return new PetCreateController($c['entityManager']);
        };

        $container[PetDeleteController::class] = function (Container $c) {
            return new PetDeleteController($c['entityManager']);
        };

        return new PsrContainer($container);
    }
}

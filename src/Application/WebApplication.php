<?php
namespace Mrubiosan\PetStore\Application;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Mrubiosan\PetStore\Presentation\PetCreateController;
use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Slim\App;
use Slim\Factory\AppFactory;

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

    private function addRoutes(App $app)
    {
        $app->post('/pet', PetCreateController::class.':create');
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

        return new PsrContainer($container);
    }
}

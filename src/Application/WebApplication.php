<?php
namespace Mrubiosan\PetStore\Application;

use Slim\App;
use Slim\Factory\AppFactory;

class WebApplication
{
    public static function create() : App
    {
        $app = AppFactory::create();

        return $app;
    }
}
